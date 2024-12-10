<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\MediaCollections\Models\Media;


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
