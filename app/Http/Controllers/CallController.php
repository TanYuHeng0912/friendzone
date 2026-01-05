<?php

namespace App\Http\Controllers;

use App\Call;
use App\Chat;
use App\Events\CallAnswer;
use App\Events\CallEnded;
use App\Events\CallIceCandidate;
use App\Events\CallOffer;
use App\Events\CallStarted;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CallController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Initiate a voice call
     */
    public function initiate(Request $request): JsonResponse
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'chat_id' => 'nullable|exists:chats,id',
            'type' => 'required|in:voice,video',
            'offer' => 'nullable|string'
        ]);

        $caller = auth()->user();
        $receiverId = $request->receiver_id;
        $chatId = $request->chat_id;
        $type = $request->type;

        // Check if users are matched (if chat_id is provided, verify they're in the chat)
        if ($chatId) {
            $chat = Chat::findOrFail($chatId);
            if (!$chat->hasUser($caller->id) || !$chat->hasUser($receiverId)) {
                return response()->json(['error' => 'Invalid chat'], 403);
            }
        }

        // Check if there's already an active call (only check for truly active calls)
        // Only check for calls that are actually active (ringing or answered) and haven't ended
        $activeCall = Call::where(function($query) use ($caller, $receiverId) {
            $query->where(function($q) use ($caller, $receiverId) {
                $q->where('caller_id', $caller->id)->where('receiver_id', $receiverId);
            })->orWhere(function($q) use ($caller, $receiverId) {
                $q->where('caller_id', $receiverId)->where('receiver_id', $caller->id);
            });
        })
        ->whereIn('status', ['ringing', 'answered'])
        ->whereNull('ended_at') // Make sure it hasn't ended
        ->where('created_at', '>', now()->subSeconds(30)) // Only check very recent calls (within 30 seconds)
        ->orderBy('created_at', 'desc')
        ->first();

        if ($activeCall) {
            // Refresh from database to get latest status
            $activeCall->refresh();
            
            // If call status is ended, rejected, or missed, allow new call
            if (in_array($activeCall->status, ['ended', 'rejected', 'missed'])) {
                // Ensure it's marked as ended
                if ($activeCall->status !== 'ended' || !$activeCall->ended_at) {
                    $activeCall->status = 'ended';
                    $activeCall->ended_at = now();
                    $activeCall->save();
                }
            } 
            // If call has ended_at timestamp, allow new call
            else if ($activeCall->ended_at) {
                // Allow new call
            }
            // Otherwise, there's an active call
            else {
                return response()->json(['error' => 'Call already in progress'], 400);
            }
        }

        // Create call record
        $call = Call::create([
            'caller_id' => $caller->id,
            'receiver_id' => $receiverId,
            'chat_id' => $chatId,
            'type' => $type,
            'status' => 'ringing'
        ]);

        // Broadcast call offer
        broadcast(new CallOffer(
            $call->id,
            $caller->id,
            $caller->info->name ?? 'User',
            $receiverId,
            $chatId,
            $type,
            $request->input('offer') // WebRTC offer from client
        ));

        return response()->json([
            'success' => true,
            'call_id' => $call->id
        ]);
    }

    /**
     * Answer a call
     */
    public function answer(Request $request, $callId): JsonResponse
    {
        $request->validate([
            'answer' => 'required|string',
            'accepted' => 'boolean'
        ]);

        $user = auth()->user();
        $call = Call::findOrFail($callId);

        // Verify user is the receiver
        if ($call->receiver_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($call->status !== 'ringing') {
            return response()->json(['error' => 'Call is not ringing'], 400);
        }

        $accepted = $request->input('accepted', true);

        if ($accepted) {
            $call->status = 'answered';
            $call->started_at = now();
            $call->save();

            // Broadcast call started
            broadcast(new CallStarted($call->id, $call->caller_id, $call->receiver_id));
        } else {
            $call->status = 'rejected';
            $call->ended_at = now();
            $call->save();
        }

        // Broadcast answer (WebRTC answer)
        broadcast(new CallAnswer($call->id, $user->id, $request->answer, $accepted));

        return response()->json([
            'success' => true,
            'call' => $call
        ]);
    }

    /**
     * End a call
     */
    public function end($callId): JsonResponse
    {
        $user = auth()->user();
        $call = Call::findOrFail($callId);

        // Verify user is part of the call
        if ($call->caller_id !== $user->id && $call->receiver_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Allow ending even if status is not active (in case of cleanup)
        // Allow ending even if status is not active (in case of cleanup)
        if (!in_array($call->status, ['ringing', 'answered', 'ended'])) {
            // If already ended, just broadcast again
            if ($call->status === 'ended') {
                broadcast(new CallEnded($call->id, $user->id, $call->duration));
                return response()->json([
                    'success' => true,
                    'call' => $call
                ]);
            }
            return response()->json(['error' => 'Call is not active'], 400);
        }

        // End the call - always set status to ended and timestamp
        $call->status = 'ended';
        $call->ended_at = now();
        
        if ($call->started_at) {
            $call->duration = $call->started_at->diffInSeconds($call->ended_at);
        } else {
            $call->duration = 0;
        }
        
        $call->save();
        
        // Also mark any other active calls between these users as ended
        Call::where(function($query) use ($call) {
            $query->where(function($q) use ($call) {
                $q->where('caller_id', $call->caller_id)->where('receiver_id', $call->receiver_id);
            })->orWhere(function($q) use ($call) {
                $q->where('caller_id', $call->receiver_id)->where('receiver_id', $call->caller_id);
            });
        })
        ->whereIn('status', ['ringing', 'answered'])
        ->whereNull('ended_at')
        ->where('id', '!=', $call->id)
        ->update([
            'status' => 'ended',
            'ended_at' => now()
        ]);

        // Broadcast call ended
        broadcast(new CallEnded($call->id, $user->id, $call->duration));

        return response()->json([
            'success' => true,
            'call' => $call
        ]);
    }

    /**
     * Send ICE candidate
     */
    public function iceCandidate(Request $request, $callId): JsonResponse
    {
        $request->validate([
            'candidate' => 'required|string'
        ]);

        $user = auth()->user();
        $call = Call::findOrFail($callId);

        // Verify user is part of the call
        if ($call->caller_id !== $user->id && $call->receiver_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Broadcast ICE candidate
        broadcast(new CallIceCandidate($call->id, $user->id, $request->candidate));

        return response()->json(['success' => true]);
    }
}
