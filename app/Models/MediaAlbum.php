<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MediaAlbum extends Model
{
    protected $fillable = [
        'user_id',
    ];

    public function media(): HasMany
    {
        return $this->HasMany(Media::class);
    }
}
