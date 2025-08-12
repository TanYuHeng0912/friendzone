<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Community extends Model
{
    protected $fillable = [
        'name',
        'slug', 
        'description',
        'icon'
    ];

    public function members()
    {
        return $this->belongsToMany('App\User', 'community_members');
    }

    public function posts()
    {
        return $this->hasMany('App\Post');
    }

    public function isMember($userId)
    {
        return $this->members()->where('user_id', $userId)->exists();
    }

    public function getMembersCountAttribute()
    {
        return $this->members()->count();
    }

    public function getPostsCountAttribute()
    {
        return $this->posts()->count();
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}