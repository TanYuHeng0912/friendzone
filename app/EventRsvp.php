<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventRsvp extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
        'status'
    ];
    
    public function event()
    {
        return $this->belongsTo('App\Event');
    }
    
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
