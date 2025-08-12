<?php

namespace App\Http\Controllers;

use App\Chat;
use App\Message;
use App\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display all chats for the authenticated user
     */
    public function index(): View
    {
        $user = auth()->user();
        
        $chats = Chat::where(function($query) use ($user) {
            $query->where('user_one', $user->id)
                  ->orWhere('user_two', $user->id);
        })
        ->with(['userOne.info', 'userTwo.info', 'latestMessage'])
        ->orderBy('last_message_at', 'desc')
        ->get();

        return view('chat.index', compact('chats', 'user'));
    }

    /**
     * Show a specific chat room
     */
    public function show($chatId): View
    {
        $user = auth()->user();
        
        $chat = Chat::with(['userOne.info', 'userTwo.info', 'messages.sender'])
            ->findOrFail($chatId);

        // Check if user is participant in this chat
        if (!$chat->hasUser($user->id)) {
            abort(403, 'Unauthorized access to this chat.');
        }

        // Mark messages as read
        $chat->markAsRead($user->id);

        $otherUser = $chat->getOtherUser($user->id);

        return view('chat.show', compact('chat', 'user', 'otherUser'));
    }

    /**
     * Create or get existing chat between two matched users
     */
    public function createChat($userId)
    {
        $currentUser = auth()->user();
        $otherUser = User::findOrFail($userId);

        // Check if users are matched
        if (!$this->areUsersMatched($currentUser->id, $otherUser->id)) {
            return redirect()->back()->with('error', 'You can only chat with matched users.');
        }

        // Find existing chat or create new one
        $chat = Chat::where(function($query) use ($currentUser, $otherUser) {
            $query->where('user_one', $currentUser->id)->where('user_two', $otherUser->id);
        })->orWhere(function($query) use ($currentUser, $otherUser) {
            $query->where('user_one', $otherUser->id)->where('user_two', $currentUser->id);
        })->first();

        if (!$chat) {
            $chat = Chat::create([
                'user_one' => min($currentUser->id, $otherUser->id),
                'user_two' => max($currentUser->id, $otherUser->id),
                'last_message_at' => now()
            ]);
        }

        return redirect()->route('chat.show', $chat->id);
    }

    /**
     * Send a message
     */
    public function sendMessage(Request $request, $chatId): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $user = auth()->user();
        $chat = Chat::findOrFail($chatId);

        // Check if user is participant in this chat
        if (!$chat->hasUser($user->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message = Message::create([
            'chat_id' => $chat->id,
            'sender_id' => $user->id,
            'message' => $request->message,
            'is_read' => false
        ]);

        // Update chat's last message timestamp
        $chat->update(['last_message_at' => now()]);

        // Load sender relationship for response
        $message->load('sender');

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'message' => $message->message,
                'sender_name' => $message->sender->info->name ?? 'User',
                'sender_id' => $message->sender_id,
                'formatted_time' => $message->formatted_time,
                'created_at' => $message->created_at->toISOString()
            ]
        ]);
    }

    /**
     * Get new messages for a chat (for AJAX polling)
     */
    public function getMessages($chatId, $lastMessageId = 0): JsonResponse
    {
        $user = auth()->user();
        $chat = Chat::findOrFail($chatId);

        if (!$chat->hasUser($user->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $messages = $chat->messages()
            ->with('sender')
            ->where('id', '>', $lastMessageId)
            ->get();

        // Mark new messages as read
        $chat->markAsRead($user->id);

        return response()->json([
            'messages' => $messages->map(function($message) {
                return [
                    'id' => $message->id,
                    'message' => $message->message,
                    'sender_name' => $message->sender->info->name ?? 'User',
                    'sender_id' => $message->sender_id,
                    'formatted_time' => $message->formatted_time,
                    'created_at' => $message->created_at->toISOString()
                ];
            })
        ]);
    }

    /**
     * Check if two users are matched
     */
    private function areUsersMatched($userId1, $userId2): bool
    {
        $user1 = User::find($userId1);
        $user2 = User::find($userId2);

        return $user1 && $user2 && $user1->match($user2) !== null;
    }


public function sendVoiceMessage(Request $request, $chatId): JsonResponse
{
    try {
        \Log::info('Voice message request received for chat: ' . $chatId);
        
        $request->validate([
            'voice_message' => 'required|file|mimes:wav,mp3,ogg,webm|max:10240'
        ]);

        $user = auth()->user();
        $chat = Chat::findOrFail($chatId);

        // Check if user is participant in this chat
        if (!$chat->hasUser($user->id)) {
            \Log::warning('Unauthorized voice message attempt');
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Store the voice message file
        $file = $request->file('voice_message');
        $filename = time() . '_' . $user->id . '_voice.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('voice_messages', $filename, 'public');

        $message = Message::create([
            'chat_id' => $chat->id,
            'sender_id' => $user->id,
            'message' => $path,
            'message_type' => 'voice',
            'is_read' => false
        ]);

        // Update chat's last message timestamp
        $chat->update(['last_message_at' => now()]);
        $message->load('sender');

        \Log::info('Voice message saved successfully');

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'message' => $message->message,
                'message_type' => $message->message_type,
                'sender_name' => $message->sender->info->name ?? 'User',
                'sender_id' => $message->sender_id,
                'formatted_time' => $message->formatted_time,
                'created_at' => $message->created_at->toISOString(),
                'voice_url' => asset('storage/' . $message->message)
            ]
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Voice message error: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());
        
        return response()->json([
            'success' => false,
            'error' => 'Server error: ' . $e->getMessage()
        ], 500);
    }
}

}