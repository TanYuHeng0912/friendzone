<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallAnswer implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $callId;
    public $answererId;
    public $answer;
    public $accepted;

    public function __construct($callId, $answererId, $answer, $accepted = true)
    {
        $this->callId = $callId;
        $this->answererId = $answererId;
        $this->answer = $answer;
        $this->accepted = $accepted;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('call.' . $this->callId);
    }

    public function broadcastAs()
    {
        return 'call.answer';
    }
}
