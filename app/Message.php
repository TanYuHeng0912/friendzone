<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'chat_id',
        'sender_id',
        'message',
        'message_type',
        'is_read'
    ];

    protected $casts = [
        'is_read' => 'boolean'
    ];

    public function chat()
    {
        return $this->belongsTo('App\Chat');
    }

    public function sender()
    {
        return $this->belongsTo('App\User', 'sender_id');
    }

    // Format the message timestamp
    public function getFormattedTimeAttribute()
    {
        return $this->created_at->format('H:i');
    }

    // Format the message date
    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('M d, Y');
    }
    public function isVoiceMessage()
{
    return $this->message_type === 'voice';
}

// Add this method to get voice URL
public function getVoiceUrlAttribute()
{
    if ($this->isVoiceMessage()) {
        return asset('storage/' . $this->message);
    }
    return null;
}
}