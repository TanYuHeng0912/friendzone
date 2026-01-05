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
        'suspended_until',
        'is_online',
        'last_seen_at'
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
        'is_online' => 'boolean',
        'last_seen_at' => 'datetime',
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
        // user_one = the user who dislikes (current user)
        // user_two = the user being disliked
        return $this->belongsToMany('App\User', 'dislikes', 'user_one', 'user_two');
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
        // Exclude users that the current user has already liked
        // Check directly in the matches table where user_one = current user and user_two = other user
        $query->whereNotExists(function ($subquery) use ($id) {
            $subquery->select(\Illuminate\Support\Facades\DB::raw(1))
                ->from('matches')
                ->whereColumn('matches.user_two', 'users.id')
                ->where('matches.user_one', $id);
        });
        
        // Exclude users that the current user has already disliked
        // Check directly in the dislikes table where user_one = current user and user_two = other user
        $query->whereNotExists(function ($subquery) use ($id) {
            $subquery->select(\Illuminate\Support\Facades\DB::raw(1))
                ->from('dislikes')
                ->whereColumn('dislikes.user_two', 'users.id')
                ->where('dislikes.user_one', $id);
        });
        
        return $query;
    }

    public function scopeSearchMatches($query, $id)
    {
        // A match exists when:
        // 1. Current user (id) has liked the other user (matches.user_one = id, matches.user_two = other user)
        // 2. Other user has liked current user (matches.user_one = other user, matches.user_two = id)
        return $query->whereExists(function ($subquery) use ($id) {
            // Check if current user has liked this user
            $subquery->select(\Illuminate\Support\Facades\DB::raw(1))
                ->from('matches')
                ->whereColumn('matches.user_two', 'users.id')
                ->where('matches.user_one', $id);
        })
        ->whereExists(function ($subquery) use ($id) {
            // Check if this user has liked current user
            $subquery->select(\Illuminate\Support\Facades\DB::raw(1))
                ->from('matches')
                ->whereColumn('matches.user_one', 'users.id')
                ->where('matches.user_two', $id);
        })
        ->whereNotExists(function ($subquery) use ($id) {
            // Exclude if current user has disliked this user
            $subquery->select(\Illuminate\Support\Facades\DB::raw(1))
                ->from('dislikes')
                ->whereColumn('dislikes.user_two', 'users.id')
                ->where('dislikes.user_one', $id);
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
            ->whereNotExists(function ($subquery) use ($id) {
                $subquery->select(\Illuminate\Support\Facades\DB::raw(1))
                    ->from('dislikes')
                    ->whereColumn('dislikes.user_two', 'users.id')
                    ->where('dislikes.user_one', $id);
            });
    }
    
    // Friend relationships
    public function friendships()
    {
        return $this->hasMany('App\Friendship', 'user_id');
    }
    
    public function friendRequests()
    {
        return $this->hasMany('App\Friendship', 'friend_id')->where('status', 'pending');
    }
    
    public function friends()
    {
        return $this->belongsToMany('App\User', 'friendships', 'user_id', 'friend_id')
            ->wherePivot('status', 'accepted')
            ->orWhere(function($query) {
                $query->where('friendships.user_id', $this->id)
                      ->where('friendships.status', 'accepted');
            });
    }
    
    // Activity relationships
    public function activities()
    {
        return $this->hasMany('App\Activity');
    }
    
    // Check if user is online (active within last 5 minutes)
    public function isOnline()
    {
        if (!$this->last_seen_at) {
            return false;
        }
        return $this->last_seen_at->diffInMinutes(now()) <= 5;
    }
    
    // Update last seen timestamp
    public function updateLastSeen()
    {
        $this->update([
            'last_seen_at' => now(),
            'is_online' => true
        ]);
    }
}