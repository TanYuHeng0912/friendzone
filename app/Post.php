<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    protected $fillable = [
        'title',
        'content',
        'image',
        'user_id',
        'community_id',
        'likes_count',
        'comments_count'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function community()
    {
        return $this->belongsTo('App\Community');
    }

    public function comments()
    {
        return $this->hasMany('App\Comment')->whereNull('parent_id')->latest();
    }

    public function allComments()
    {
        return $this->hasMany('App\Comment');
    }

    public function likes()
    {
        return $this->belongsToMany('App\User', 'post_likes');
    }

    public function isLikedBy($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    public function getImageUrl()
    {
        if ($this->image) {
            return Storage::url($this->image);
        }
        return null;
    }

    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    // Update counters when likes/comments change
    public function updateLikesCount()
    {
        $count = $this->likes()->count();
        $this->update(['likes_count' => $count]);
    }

    public function updateCommentsCount()
    {
        $count = $this->allComments()->count();
        $this->update(['comments_count' => $count]);
    }
}