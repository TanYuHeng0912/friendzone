<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PollVote extends Model
{
    protected $fillable = [
        'poll_id',
        'user_id',
        'option_index'
    ];
    
    public function poll()
    {
        return $this->belongsTo('App\Poll');
    }
    
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
