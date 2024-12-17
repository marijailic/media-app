<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Media;

class MediaController extends Controller
{
    public function destroy(Media $media)
    {
        $media->delete();

        return response()->noContent(200);
    }
}
