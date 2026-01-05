<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'post_id',
        'community_id',
        'title',
        'description',
        'start_time',
        'end_time',
        'location',
        'max_attendees'
    ];
    
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];
    
    public function post()
    {
        return $this->belongsTo('App\Post');
    }
    
    public function community()
    {
        return $this->belongsTo('App\Community');
    }
    
    public function rsvps()
    {
        return $this->hasMany('App\EventRsvp');
    }
    
    public function goingCount()
    {
        return $this->rsvps()->where('status', 'going')->count();
    }
    
    public function interestedCount()
    {
        return $this->rsvps()->where('status', 'interested')->count();
    }
    
    public function hasRsvped($userId)
    {
        return $this->rsvps()->where('user_id', $userId)->exists();
    }
    
    public function getUserRsvp($userId)
    {
        return $this->rsvps()->where('user_id', $userId)->first();
    }
    
    public function isFull()
    {
        return $this->max_attendees && $this->goingCount() >= $this->max_attendees;
    }
    
    public function isPast()
    {
        return $this->start_time->isPast();
    }
}
