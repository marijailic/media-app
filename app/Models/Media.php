<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Media extends Model
{
    protected $fillable = [
        'model_id', // id albuma
        'name', // 'description'
        'file_name',
    ];

    public function mediaAlbum(): BelongsTo
    {
        return $this->belongsTo(MediaAlbum::class);
    }
}
