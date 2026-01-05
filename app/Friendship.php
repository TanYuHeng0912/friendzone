<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Friendship extends Model
{
    protected $fillable = [
        'user_id',
        'friend_id',
        'status',
        'requester_id'
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
    
    public function friend()
    {
        return $this->belongsTo('App\User', 'friend_id');
    }
    
    public function requester()
    {
        return $this->belongsTo('App\User', 'requester_id');
    }
    
    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }
    
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
