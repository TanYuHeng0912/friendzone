<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    protected $fillable = [
        'post_id',
        'question',
        'options',
        'ends_at'
    ];
    
    protected $casts = [
        'options' => 'array',
        'ends_at' => 'datetime',
    ];
    
    public function post()
    {
        return $this->belongsTo('App\Post');
    }
    
    public function votes()
    {
        return $this->hasMany('App\PollVote');
    }
    
    public function getResults()
    {
        $results = [];
        $totalVotes = $this->votes()->count();
        
        foreach ($this->options as $index => $option) {
            $voteCount = $this->votes()->where('option_index', $index)->count();
            $results[] = [
                'option' => $option,
                'votes' => $voteCount,
                'percentage' => $totalVotes > 0 ? round(($voteCount / $totalVotes) * 100, 1) : 0
            ];
        }
        
        return $results;
    }
    
    public function hasVoted($userId)
    {
        return $this->votes()->where('user_id', $userId)->exists();
    }
    
    public function isExpired()
    {
        return $this->ends_at && $this->ends_at->isPast();
    }
}
