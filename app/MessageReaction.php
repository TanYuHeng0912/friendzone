<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MessageReaction extends Model
{
    protected $fillable = [
        'message_id',
        'user_id',
        'reaction'
    ];

    public function message()
    {
        return $this->belongsTo('App\Message');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}

