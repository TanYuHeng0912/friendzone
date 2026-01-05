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
