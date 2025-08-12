<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'content',
        'user_id',
        'post_id',
        'parent_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function post()
    {
        return $this->belongsTo('App\Post');
    }

    public function parent()
    {
        return $this->belongsTo('App\Comment', 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany('App\Comment', 'parent_id')->latest();
    }

    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}