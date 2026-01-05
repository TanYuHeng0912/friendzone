<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Chat channel authorization
Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    $chat = \App\Chat::find($chatId);
    if (!$chat) {
        return false;
    }
    // Only allow users who are participants in the chat
    return $chat->hasUser($user->id);
});

// User channel for receiving calls
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Call channel for WebRTC signaling
Broadcast::channel('call.{callId}', function ($user, $callId) {
    $call = \App\Call::find($callId);
    if (!$call) {
        return false;
    }
    // Only allow caller and receiver
    return $call->caller_id === $user->id || $call->receiver_id === $user->id;
});
