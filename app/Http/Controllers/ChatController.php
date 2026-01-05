<?php

namespace App\Http\Controllers;

use App\Chat;
use App\Message;
use App\User;
use App\Events\MessageSent;
use App\Events\UserTyping;
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
        
        $chat = Chat::with(['userOne.info', 'userTwo.info', 'messages.sender.info', 'messages.replyTo.sender.info', 'messages.reactions'])
            ->findOrFail($chatId);

        // Check if user is participant in this chat
        if (!$chat->hasUser($user->id)) {
            abort(403, 'Unauthorized access to this chat.');
        }

        // Mark messages as read
        $chat->markAsRead($user->id);

        $otherUser = $chat->getOtherUser($user->id);
        
        // Get call history for this chat
        $calls = \App\Call::where('chat_id', $chatId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('chat.show', compact('chat', 'user', 'otherUser', 'calls'));
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
            'message' => 'nullable|string|max:1000',
            'reply_to_id' => 'nullable|exists:messages,id',
            'media' => 'nullable|file|mimes:jpeg,jpg,png,gif,mp4,mov,avi|max:10240', // 10MB max
            'media_type' => 'nullable|in:image,video,gif'
        ]);

        $user = auth()->user();
        $chat = Chat::findOrFail($chatId);

        // Check if user is participant in this chat
        if (!$chat->hasUser($user->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Verify reply message belongs to this chat if provided
        $replyToId = null;
        if ($request->has('reply_to_id')) {
            $replyTo = Message::findOrFail($request->reply_to_id);
            if ($replyTo->chat_id != $chat->id) {
                return response()->json(['error' => 'Invalid reply message'], 400);
            }
            $replyToId = $request->reply_to_id;
        }

        // Handle media upload
        $mediaPath = null;
        $mediaType = null;
        $mediaThumbnail = null;
        
        if ($request->hasFile('media')) {
            $file = $request->file('media');
            $mediaType = $request->media_type ?? ($file->getMimeType() === 'image/gif' ? 'gif' : (str_starts_with($file->getMimeType(), 'image/') ? 'image' : 'video'));
            
            $path = $file->store('chat_media', 'public');
            $mediaPath = $path;
            
            // Generate thumbnail for videos
            if ($mediaType === 'video') {
                // For now, we'll use a placeholder. In production, use FFmpeg to generate thumbnails
                $mediaThumbnail = 'chat_media/thumbnails/' . basename($path) . '.jpg';
            }
        }

        $message = Message::create([
            'chat_id' => $chat->id,
            'sender_id' => $user->id,
            'message' => $request->message ?? '',
            'reply_to_id' => $replyToId,
            'is_read' => false,
            'media_type' => $mediaType,
            'media_path' => $mediaPath,
            'media_thumbnail' => $mediaThumbnail
        ]);

        // Update chat's last message timestamp
        $chat->update(['last_message_at' => now()]);

        // Load relationships for response
        $message->load(['sender.info', 'replyTo.sender.info', 'reactions']);

        // Broadcast the message event
        try {
            broadcast(new MessageSent($message, $chatId))->toOthers();
        } catch (\Exception $e) {
            // If broadcasting fails, continue with normal response
            \Log::warning('Broadcasting failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => $this->formatMessage($message)
        ]);
    }

    /**
     * Get new messages for a chat (for AJAX polling)
     */
    public function getMessages(Request $request, $chatId): JsonResponse
    {
        $user = auth()->user();
        $chat = Chat::findOrFail($chatId);

        if (!$chat->hasUser($user->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $lastMessageId = $request->query('after', 0);

        $messages = $chat->messages()
            ->with(['sender.info', 'replyTo.sender.info', 'reactions'])
            ->where('id', '>', $lastMessageId)
            ->orderBy('id', 'asc')
            ->get();

        // Mark new messages as read
        $chat->markAsRead($user->id);

        return response()->json([
            'messages' => $messages->map(function($message) {
                return $this->formatMessage($message);
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
    
    /**
     * Format message for JSON response
     */
    private function formatMessage($message): array
    {
        return [
            'id' => $message->id,
            'message' => $message->message,
            'message_type' => $message->message_type,
            'sender_name' => $message->sender->info->name ?? 'User',
            'sender_id' => $message->sender_id,
            'reply_to' => $message->replyTo ? [
                'id' => $message->replyTo->id,
                'message' => $message->replyTo->message,
                'sender_name' => $message->replyTo->sender->info->name ?? 'User'
            ] : null,
            'formatted_time' => $message->formatted_time,
            'created_at' => $message->created_at->toISOString(),
            'voice_url' => $message->isVoiceMessage() ? $message->voice_url : null,
            'media_type' => $message->media_type,
            'media_path' => $message->media_path ? asset('storage/' . $message->media_path) : null,
            'media_thumbnail' => $message->media_thumbnail ? asset('storage/' . $message->media_thumbnail) : null,
            'reactions' => $message->reactions->groupBy('reaction')->map(function($reactions) {
                return $reactions->count();
            })
        ];
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
            $message->load(['sender.info', 'reactions']);

            // Broadcast the voice message event
            try {
                broadcast(new MessageSent($message, $chatId))->toOthers();
            } catch (\Exception $e) {
                \Log::warning('Broadcasting voice message failed: ' . $e->getMessage());
            }

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

    /**
     * Set typing indicator
     */
    public function setTyping(Request $request, $chatId): JsonResponse
    {
        $user = auth()->user();
        $chat = Chat::findOrFail($chatId);

        if (!$chat->hasUser($user->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $isTyping = $request->get('is_typing', true);
        $chat->setTyping($user->id);

        // Broadcast typing event
        try {
            broadcast(new UserTyping($user->id, $user->info->name ?? 'User', $chatId, $isTyping))->toOthers();
        } catch (\Exception $e) {
            \Log::warning('Broadcasting typing failed: ' . $e->getMessage());
        }

        return response()->json(['success' => true]);
    }

    /**
     * Get typing status
     */
    public function getTypingStatus($chatId): JsonResponse
    {
        $user = auth()->user();
        $chat = Chat::findOrFail($chatId);

        if (!$chat->hasUser($user->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $typingUserId = $chat->getTypingUser();
        $isTyping = $typingUserId && $typingUserId != $user->id;

        return response()->json([
            'is_typing' => $isTyping,
            'typing_user' => $isTyping ? $chat->getOtherUser($user->id)->info->name : null
        ]);
    }

    /**
     * Add reaction to message
     */
    public function addReaction(Request $request, $messageId): JsonResponse
    {
        $request->validate([
            'reaction' => 'required|string|max:10'
        ]);

        $user = auth()->user();
        $message = Message::findOrFail($messageId);
        $chat = $message->chat;

        if (!$chat->hasUser($user->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $reaction = \App\MessageReaction::firstOrCreate([
            'message_id' => $message->id,
            'user_id' => $user->id,
            'reaction' => $request->reaction
        ]);

        return response()->json([
            'success' => true,
            'reaction' => $reaction
        ]);
    }

    /**
     * Remove reaction from message
     */
    public function removeReaction($messageId, $reaction): JsonResponse
    {
        $user = auth()->user();
        $message = Message::findOrFail($messageId);
        $chat = $message->chat;

        if (!$chat->hasUser($user->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        \App\MessageReaction::where('message_id', $message->id)
            ->where('user_id', $user->id)
            ->where('reaction', $reaction)
            ->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Reply to a message
     */
    public function replyToMessage(Request $request, $chatId): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'reply_to_id' => 'required|exists:messages,id'
        ]);

        $user = auth()->user();
        $chat = Chat::findOrFail($chatId);

        if (!$chat->hasUser($user->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Verify reply message belongs to this chat
        $replyTo = Message::findOrFail($request->reply_to_id);
        if ($replyTo->chat_id != $chat->id) {
            return response()->json(['error' => 'Invalid reply message'], 400);
        }

        $message = Message::create([
            'chat_id' => $chat->id,
            'sender_id' => $user->id,
            'message' => $request->message,
            'reply_to_id' => $request->reply_to_id,
            'is_read' => false
        ]);

        $chat->update(['last_message_at' => now()]);
        $message->load(['sender', 'replyTo.sender']);

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'message' => $message->message,
                'sender_name' => $message->sender->info->name ?? 'User',
                'sender_id' => $message->sender_id,
                'reply_to' => $message->replyTo ? [
                    'id' => $message->replyTo->id,
                    'message' => $message->replyTo->message,
                    'sender_name' => $message->replyTo->sender->info->name ?? 'User'
                ] : null,
                'formatted_time' => $message->formatted_time,
                'created_at' => $message->created_at->toISOString()
            ]
        ]);
    }

    /**
     * Search messages in chat
     */
    public function searchMessages(Request $request, Chat $chat): JsonResponse
    {
        try {
            $request->validate([
                'query' => 'required|string|min:1|max:100'
            ]);

            $user = auth()->user();

            if (!$chat->hasUser($user->id)) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $query = $request->input('query');
            
            // Search in message text (excluding null/empty messages)
            $messages = $chat->messages()
                ->whereNotNull('message')
                ->where('message', '!=', '')
                ->where('message', 'like', '%' . $query . '%')
                ->with(['sender.info'])
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();

            return response()->json([
                'messages' => $messages->map(function($message) {
                    return [
                        'id' => $message->id,
                        'message' => $message->message,
                        'sender_name' => $message->sender->info->name ?? 'User',
                        'formatted_time' => $message->formatted_time ?? $message->created_at->setTimezone('Asia/Kuala_Lumpur')->format('H:i'),
                        'created_at' => $message->created_at->toISOString()
                    ];
                })
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Search error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Search failed: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Search GIFs using Giphy API (or similar service)
     */
    public function searchGifs(Request $request, $chatId): JsonResponse
    {
        $query = $request->get('q', '');
        
        if (empty($query)) {
            return response()->json(['gifs' => []]);
        }
        
        // For now, return mock data. In production, integrate with Giphy API
        // Add GIPHY_API_KEY to .env for real integration
        $apiKey = env('GIPHY_API_KEY', '');
        
        if (empty($apiKey)) {
            // Return mock data for development
            return response()->json([
                'gifs' => [
                    [
                        'id' => 'mock1',
                        'url' => 'https://media.giphy.com/media/example1.gif',
                        'preview' => 'https://media.giphy.com/media/example1_small.gif'
                    ],
                    [
                        'id' => 'mock2',
                        'url' => 'https://media.giphy.com/media/example2.gif',
                        'preview' => 'https://media.giphy.com/media/example2_small.gif'
                    ]
                ]
            ]);
        }
        
        // Real Giphy API integration
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->get('https://api.giphy.com/v1/gifs/search', [
                'query' => [
                    'api_key' => $apiKey,
                    'q' => $query,
                    'limit' => 20
                ]
            ]);
            
            $data = json_decode($response->getBody(), true);
            
            $gifs = array_map(function($gif) {
                return [
                    'id' => $gif['id'],
                    'url' => $gif['images']['original']['url'],
                    'preview' => $gif['images']['fixed_height_small']['url']
                ];
            }, $data['data'] ?? []);
            
            return response()->json(['gifs' => $gifs]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch GIFs'], 500);
        }
    }
}