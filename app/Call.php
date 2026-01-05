<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    protected $fillable = [
        'caller_id',
        'receiver_id',
        'chat_id',
        'status',
        'type',
        'started_at',
        'ended_at',
        'duration'
    ];

    protected $dates = [
        'started_at',
        'ended_at',
        'created_at',
        'updated_at'
    ];

    public function caller()
    {
        return $this->belongsTo('App\User', 'caller_id');
    }

    public function receiver()
    {
        return $this->belongsTo('App\User', 'receiver_id');
    }

    public function chat()
    {
        return $this->belongsTo('App\Chat', 'chat_id');
    }

    public function isActive()
    {
        return in_array($this->status, ['ringing', 'answered']);
    }

    public function end()
    {
        $this->status = 'ended';
        $this->ended_at = now();
        
        if ($this->started_at) {
            $this->duration = $this->started_at->diffInSeconds($this->ended_at);
        }
        
        $this->save();
    }
}
