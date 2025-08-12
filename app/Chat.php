<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = [
        'user_one',
        'user_two',
        'last_message_at'
    ];

    protected $dates = [
        'last_message_at'
    ];

    public function userOne()
    {
        return $this->belongsTo('App\User', 'user_one');
    }

    public function userTwo()
    {
        return $this->belongsTo('App\User', 'user_two');
    }

    public function messages()
    {
        return $this->hasMany('App\Message')->orderBy('created_at', 'asc');
    }

    public function latestMessage()
    {
        return $this->hasOne('App\Message')->latest();
    }

    // Get the other user in the chat
    public function getOtherUser($currentUserId)
    {
        return $this->user_one == $currentUserId ? $this->userTwo : $this->userOne;
    }

    // Check if user is participant in this chat
    public function hasUser($userId)
    {
        return $this->user_one == $userId || $this->user_two == $userId;
    }

    // Get unread messages count for a specific user
    public function getUnreadCount($userId)
    {
        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->count();
    }

    // Mark messages as read for a specific user
    public function markAsRead($userId)
    {
        $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }
}