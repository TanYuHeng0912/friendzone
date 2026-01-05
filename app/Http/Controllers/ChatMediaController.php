<?php

namespace App\Http\Controllers;

use App\Chat;
use App\Message;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class ChatMediaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display media gallery for a chat
     */
    public function index($chatId): View
    {
        $user = auth()->user();
        $chat = Chat::findOrFail($chatId);

        // Check if user is participant in this chat
        if (!$chat->hasUser($user->id)) {
            abort(403, 'Unauthorized access to this chat.');
        }

        $otherUser = $chat->getOtherUser($user->id);

        // Get all media messages
        $mediaMessages = Message::where('chat_id', $chatId)
            ->whereNotNull('media_type')
            ->whereNotNull('media_path')
            ->with('sender.info')
            ->orderBy('created_at', 'desc')
            ->get();

        // Group by media type
        $images = $mediaMessages->whereIn('media_type', ['image', 'gif']);
        $videos = $mediaMessages->where('media_type', 'video');

        return view('chat.media', compact('chat', 'otherUser', 'images', 'videos', 'mediaMessages'));
    }

    /**
     * Get media messages as JSON (for API)
     */
    public function getMedia($chatId): JsonResponse
    {
        $user = auth()->user();
        $chat = Chat::findOrFail($chatId);

        if (!$chat->hasUser($user->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $mediaMessages = Message::where('chat_id', $chatId)
            ->whereNotNull('media_type')
            ->whereNotNull('media_path')
            ->with('sender.info')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($message) {
                return [
                    'id' => $message->id,
                    'media_type' => $message->media_type,
                    'media_path' => asset('storage/' . $message->media_path),
                    'media_thumbnail' => $message->media_thumbnail ? asset('storage/' . $message->media_thumbnail) : null,
                    'sender_name' => $message->sender->info->name ?? 'User',
                    'created_at' => $message->created_at->toISOString(),
                    'formatted_time' => $message->formatted_time
                ];
            });

        return response()->json(['media' => $mediaMessages]);
    }
}
