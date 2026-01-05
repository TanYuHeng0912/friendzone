<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserMatch extends Model
{
    protected $table = 'matches';
    
    protected $fillable = [
        'user_one',
        'user_two',
        'is_super_like'
    ];

    protected $casts = [
        'is_super_like' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_one');
    }
}
