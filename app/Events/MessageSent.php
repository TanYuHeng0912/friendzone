<?php

namespace App\Events;

use App\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $chatId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Message $message, $chatId)
    {
        $this->message = $message;
        $this->chatId = $chatId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('chat.' . $this->chatId);
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'message.sent';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        $this->message->load(['sender.info', 'replyTo.sender.info', 'reactions']);
        
        return [
            'id' => $this->message->id,
            'message' => $this->message->message,
            'message_type' => $this->message->message_type,
            'sender_name' => $this->message->sender->info->name ?? 'User',
            'sender_id' => $this->message->sender_id,
            'reply_to' => $this->message->replyTo ? [
                'id' => $this->message->replyTo->id,
                'message' => $this->message->replyTo->message,
                'sender_name' => $this->message->replyTo->sender->info->name ?? 'User'
            ] : null,
            'formatted_time' => $this->message->formatted_time,
            'created_at' => $this->message->created_at->toISOString(),
            'voice_url' => $this->message->isVoiceMessage() ? $this->message->voice_url : null,
            'media_type' => $this->message->media_type,
            'media_path' => $this->message->media_path ? asset('storage/' . $this->message->media_path) : null,
            'media_thumbnail' => $this->message->media_thumbnail ? asset('storage/' . $this->message->media_thumbnail) : null,
            'reactions' => $this->message->reactions->groupBy('reaction')->map(function($reactions) {
                return $reactions->count();
            })
        ];
    }
}
