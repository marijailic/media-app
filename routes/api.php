<?php

use App\Http\Controllers\Api\MediaAlbumController;
use App\Http\Controllers\Api\MediaController;
use Illuminate\Support\Facades\Route;

Route::apiResource('media-album', MediaAlbumController::class)->except(['update']);

Route::delete('media-album/{media_album}/media/{media}', [MediaAlbumController::class, 'destroyMedia'])
    ->scopeBindings()
    ->name('media-album.destroyMedia');
