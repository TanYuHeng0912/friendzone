<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class UserInfo extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'surname',
        'phone',
        'age',
        'gender',
        'profile_picture',
        'description',
        'relationship',
        'country',
        'languages',
        'tag1',
        'tag2',
        'tag3'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function getPicture()
    {
        if($this->profile_picture == null || $this->profile_picture == '')
        {
            // Return default image if exists, otherwise placeholder
            $defaultPath = storage_path('app/public/picture/default.png');
            if (file_exists($defaultPath)) {
                return '/storage/picture/default.png';
            }
            // Return a data URI placeholder if no default image
            return 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200"><rect width="200" height="200" fill="#ddd"/><text x="50%" y="50%" text-anchor="middle" dy=".3em" fill="#999" font-size="14">No Image</text></svg>');
        }

        // The path stored is relative to storage/app/public
        // Remove any leading slashes first
        $path = ltrim($this->profile_picture, '/');
        
        // Check if file actually exists
        $fullPath = storage_path('app/public/' . $path);
        if (!file_exists($fullPath)) {
            // File doesn't exist, return default
            $defaultPath = storage_path('app/public/picture/default.png');
            if (file_exists($defaultPath)) {
                return '/storage/picture/default.png';
            }
            return 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200"><rect width="200" height="200" fill="#ddd"/><text x="50%" y="50%" text-anchor="middle" dy=".3em" fill="#999" font-size="14">No Image</text></svg>');
        }
        
        // Use direct path like the logo does - this works with the symlink
        return '/storage/' . $path;
    }

    public function getCompletenessPercentage()
    {
        $totalFields = 0;
        $filledFields = 0;

        // Required fields
        $fields = [
            'name' => 10,
            'surname' => 10,
            'age' => 10,
            'gender' => 10,
            'profile_picture' => 15,
            'description' => 15,
            'country' => 10,
            'languages' => 10,
            'relationship' => 10,
        ];

        foreach ($fields as $field => $weight) {
            $totalFields += $weight;
            if ($field === 'profile_picture') {
                if ($this->profile_picture != null && $this->profile_picture != '') {
                    $filledFields += $weight;
                }
            } elseif (!empty($this->$field) && $this->$field !== 'none') {
                $filledFields += $weight;
            }
        }

        // Tags (bonus points)
        $tagFields = ['tag1', 'tag2', 'tag3'];
        $tagWeight = 5;
        foreach ($tagFields as $tag) {
            $totalFields += $tagWeight;
            if (!empty($this->$tag) && $this->$tag !== 'none') {
                $filledFields += $tagWeight;
            }
        }

        return $totalFields > 0 ? round(($filledFields / $totalFields) * 100) : 0;
    }

    public function getMissingFields()
    {
        $missing = [];
        
        if (empty($this->name)) $missing[] = 'Name';
        if (empty($this->surname)) $missing[] = 'Surname';
        if (empty($this->age)) $missing[] = 'Age';
        if (empty($this->gender)) $missing[] = 'Gender';
        if (empty($this->profile_picture)) $missing[] = 'Profile Picture';
        if (empty($this->description)) $missing[] = 'Bio';
        if (empty($this->country)) $missing[] = 'Country';
        if (empty($this->languages)) $missing[] = 'Languages';
        if (empty($this->relationship)) $missing[] = 'Relationship Status';
        
        $tagCount = 0;
        foreach (['tag1', 'tag2', 'tag3'] as $tag) {
            if (!empty($this->$tag) && $this->$tag !== 'none') {
                $tagCount++;
            }
        }
        if ($tagCount < 3) {
            $missing[] = 'Interests (' . (3 - $tagCount) . ' more needed)';
        }
        
        return $missing;
    }
}
