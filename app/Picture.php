<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Picture extends Model
{
    protected $fillable = [
        'user_id',
        'path',
        'order'
    ];

    public static function boot(): void
    {
        parent::boot();

        static::deleting(function (Picture $picture) {
            Storage::delete([
                Storage::disk('public')->delete($picture->path),
            ]);
        });
    }

    public function getPicture()
    {
        if($this->path == null || $this->path == '')
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
        $path = ltrim($this->path, '/');
        
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
}
