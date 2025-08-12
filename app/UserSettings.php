<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserSettings extends Model
{
    protected $fillable = [
        'user_id',
        'search_age_from',
        'search_age_to',
        'search_male',
        'search_female',
        'search_tag1',
        'search_tag2',
        'search_tag3'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
