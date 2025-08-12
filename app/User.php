<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'email',
        'is_admin',
        'password',
        'is_banned',
        'banned_at',
        'is_suspended',
        'suspended_until'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_banned' => 'boolean',
        'banned_at' => 'datetime',
        'is_suspended' => 'boolean',
        'suspended_until' => 'datetime',
    ];

    public function info()
    {
        return $this->hasOne('App\UserInfo');
    }

    public function settings()
    {
        return $this->hasOne('App\UserSettings');
    }

    public function pictures()
    {
        return $this->hasMany('App\Picture');
    }

    public function userLiked()
    {
        return $this->belongsToMany('App\User', 'matches', 'user_two', 'user_one');
    }

    public function likedUser()
    {
        return $this->belongsToMany('App\User', 'matches', 'user_one', 'user_two');
    }

     // Chat relationships
    public function chatsAsUserOne()
    {
        return $this->hasMany('App\Chat', 'user_one');
    }

    public function chatsAsUserTwo()
    {
        return $this->hasMany('App\Chat', 'user_two');
    }

    public function sentMessages()
    {
        return $this->hasMany('App\Message', 'sender_id');
    }

    // Community relationships
    public function communities()
    {
        return $this->belongsToMany('App\Community', 'community_members');
    }

    public function posts()
    {
        return $this->hasMany('App\Post');
    }

    public function comments()
    {
        return $this->hasMany('App\Comment');
    }

    public function likedPosts()
    {
        return $this->belongsToMany('App\Post', 'post_likes');
    }

    // Feedback relationship
    public function feedback()
    {
        return $this->hasMany('App\Feedback');
    }

    // Check if user is active (not banned or suspended)
    public function isActive()
    {
        if ($this->is_banned) {
            return false;
        }

        if ($this->is_suspended && $this->suspended_until && $this->suspended_until > now()) {
            return false;
        }

        return true;
    }

    // Check if user is currently suspended
    public function isSuspended()
    {
        return $this->is_suspended && $this->suspended_until && $this->suspended_until > now();
    }
    
    public function match(User $foreignUser)
    {
        $thisMatched = $this->belongsToMany('App\User', 'matches',
            'user_one', 'user_two')
            ->where('user_one', '=', $this->id)
            ->where('user_two', '=', $foreignUser->id)->getResults();

        $matchedThis = $this->belongsToMany('App\User', 'matches',
            'user_two', 'user_one')
            ->where('user_two', '=', $this->id)
            ->where('user_one', '=', $foreignUser->id)->getResults();

        if (isset($thisMatched[0]->attributes['id']) && isset($matchedThis[0]->attributes['id'])) {
            return $foreignUser;
        } else {
            return null;
        }
    }

    public function dislikes()
    {
        return $this->belongsToMany('App\User', 'dislikes', 'user_two', 'user_one');
    }

public function scopeSearchWithSettings($query, $from, $to, $gender, $id, $searchTag1 = null, $searchTag2 = null, $searchTag3 = null)
{
    if ($gender == 'both') {
        return $query->whereHas('info', function ($query) use ($from, $to, $id, $searchTag1, $searchTag2, $searchTag3) {
            $query->where('age', '>=', $from)
                ->where('age', '<=', $to)
                ->where('user_id', '!=', $id)
                ->where('profile_picture', '!=', '')
->when($searchTag1 || $searchTag2 || $searchTag3, function ($tagQuery) use ($searchTag1, $searchTag2, $searchTag3) {
                    // Create an array of search tags, excluding 'none' and null values
                    $searchTags = array_filter([$searchTag1, $searchTag2, $searchTag3], function ($tag) {
                        return $tag !== null && $tag !== 'none' && trim($tag) !== '';
                    });
                    
                    // If no valid search tags, skip tag filtering
                    if (empty($searchTags)) {
                        return $tagQuery;
                    }
                    
                    // Match if ANY search tag matches ANY user tag
                    $tagQuery->where(function ($innerQuery) use ($searchTags) {
                        foreach ($searchTags as $searchTag) {
                            $innerQuery->orWhere('tag1', $searchTag)
                                ->orWhere('tag2', $searchTag)
                                ->orWhere('tag3', $searchTag);
                        }
                    });
                });
        });
    } else {
        return $query->whereHas('info', function ($query) use ($from, $to, $gender, $id, $searchTag1, $searchTag2, $searchTag3) {
            $query->where('age', '>=', $from)
                ->where('age', '<=', $to)
                ->where('gender', $gender)
                ->where('user_id', '!=', $id)
                ->where('profile_picture', '!=', '')
                ->when($searchTag1 || $searchTag2 || $searchTag3, function ($tagQuery) use ($searchTag1, $searchTag2, $searchTag3) {
                    // Create an array of search tags, excluding 'none' and null values
                    $searchTags = array_filter([$searchTag1, $searchTag2, $searchTag3], function ($tag) {
                        return $tag !== null && $tag !== 'none' && trim($tag) !== '';
                    });
                    
                    // If no valid search tags, skip tag filtering
                    if (empty($searchTags)) {
                        return $tagQuery;
                    }
                    
                    // Match if ANY search tag matches ANY user tag
                    $tagQuery->where(function ($innerQuery) use ($searchTags) {
                        foreach ($searchTags as $searchTag) {
                            $innerQuery->orWhere('tag1', $searchTag)
                                ->orWhere('tag2', $searchTag)
                                ->orWhere('tag3', $searchTag);
                        }
                    });
                });
        });
    }
}

    public function scopeSearchWithoutLikesAndDislikes($query, $id)
    {
        return $query->whereDoesntHave('userLiked', function ($query) use ($id) {
            $query->where('user_one', $id);
        })
            ->whereDoesntHave('dislikes', function ($query) use ($id) {
                $query->where('user_one', $id);
            });
    }

    public function scopeSearchMatches($query, $id)
    {
        return $query->whereHas('userLiked', function ($query) use ($id) {
            $query->where('user_one', $id);
        })
            ->whereHas('likedUser', function ($query) use ($id) {
                $query->where('user_two', $id);
            })
            ->whereDoesntHave('dislikes', function ($query) use ($id) {
                $query->where('user_one', $id);
            });
    }

    public function scopeSearchLikes($query, $id)
    {
        return $query->whereHas('userLiked', function ($query) use ($id) {
            $query->where('user_one', $id);
        })
            ->whereDoesntHave('likedUser', function ($query) use ($id) {
                $query->where('user_two', $id);
            })
            ->whereDoesntHave('dislikes', function ($query) use ($id) {
                $query->where('user_one', $id);
            });
    }
}