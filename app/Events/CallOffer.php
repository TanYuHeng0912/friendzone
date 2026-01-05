<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallOffer implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $callId;
    public $callerId;
    public $callerName;
    public $receiverId;
    public $chatId;
    public $type;
    public $offer;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($callId, $callerId, $callerName, $receiverId, $chatId, $type, $offer)
    {
        $this->callId = $callId;
        $this->callerId = $callerId;
        $this->callerName = $callerName;
        $this->receiverId = $receiverId;
        $this->chatId = $chatId;
        $this->type = $type;
        $this->offer = $offer;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->receiverId);
    }

    public function broadcastAs()
    {
        return 'call.offer';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'callId' => $this->callId,
            'call_id' => $this->callId,
            'callerId' => $this->callerId,
            'caller_id' => $this->callerId,
            'callerName' => $this->callerName,
            'caller_name' => $this->callerName,
            'receiverId' => $this->receiverId,
            'receiver_id' => $this->receiverId,
            'chatId' => $this->chatId,
            'chat_id' => $this->chatId,
            'type' => $this->type,
            'offer' => $this->offer
        ];
    }
}
