<?php

use App\Http\Controllers\Api\MediaAlbumController;
use Illuminate\Support\Facades\Route;

Route::post('media-album', [MediaAlbumController::class, 'store']);

