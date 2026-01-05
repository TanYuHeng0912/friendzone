<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'description',
        'related_id',
        'related_type',
        'metadata'
    ];
    
    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    
    public function related()
    {
        return $this->morphTo('related', 'related_type', 'related_id');
    }
    
    public static function createActivity($userId, $type, $description, $relatedId = null, $relatedType = null, $metadata = null)
    {
        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'description' => $description,
            'related_id' => $relatedId,
            'related_type' => $relatedType,
            'metadata' => $metadata
        ]);
    }
}
