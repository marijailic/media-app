<?php

use App\Http\Controllers\Api\MediaAlbumController;
use Illuminate\Support\Facades\Route;

Route::controller(MediaAlbumController::class)->prefix('media-album')->group(function () {

    Route::get('/thumbnails','getThumbnails')
        ->name('media-album.thumbnails');

    Route::post('/upload', 'upload')
        ->name('media-album.upload');

    Route::get('/{media_album}/media', 'getAlbumMedia')
        ->whereUuid('media_album')
        ->name('media-album.media');

    Route::delete('/{media_album}', 'delete')
        ->whereUuid('media_album')
        ->name('media-album.delete');

    Route::delete('/{media_album}/media/{media}', 'deleteMedia')
        ->whereUuid('media_album')
        ->scopeBindings()
        ->name('media-album.delete-media');

});
