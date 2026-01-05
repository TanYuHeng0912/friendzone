<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = [
        'user_one',
        'user_two',
        'last_message_at',
        'typing_user_id',
        'typing_started_at'
    ];

    protected $dates = [
        'last_message_at',
        'typing_started_at'
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

    // Set typing indicator
    public function setTyping($userId)
    {
        $this->update([
            'typing_user_id' => $userId,
            'typing_started_at' => now()
        ]);
    }

    // Clear typing indicator
    public function clearTyping()
    {
        $this->update([
            'typing_user_id' => null,
            'typing_started_at' => null
        ]);
    }

    // Get typing user
    public function getTypingUser()
    {
        if ($this->typing_user_id && $this->typing_started_at) {
            // Clear if typing started more than 5 seconds ago
            if ($this->typing_started_at->diffInSeconds(now()) > 5) {
                $this->clearTyping();
                return null;
            }
            return $this->typing_user_id;
        }
        return null;
    }
}