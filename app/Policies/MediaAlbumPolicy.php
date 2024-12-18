<?php

namespace App\Policies;

use App\Models\MediaAlbum;
use App\Models\User;

class MediaAlbumPolicy
{
    public function ownsAlbum(User $user, MediaAlbum $mediaAlbum): bool
    {
        return $mediaAlbum->user_id === $user->id;
    }
}
