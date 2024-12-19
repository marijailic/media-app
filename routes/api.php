<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MediaAlbumController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

Route::controller(MediaAlbumController::class)
    ->middleware('auth:sanctum')->prefix('media-album')->group(function () {

    Route::get('thumbnails','getThumbnails')
        ->name('media-album.thumbnails');

    Route::post('', 'upload')
        ->name('media-album.upload');

    Route::get('{media_album}', 'getAlbumMedia')
        ->whereUuid('media_album')
        ->name('media-album.media');

    Route::delete('{media_album}', 'delete')
        ->whereUuid('media_album')
        ->name('media-album.delete');

    Route::delete('{media_album}/media/{media}', 'deleteMedia')
        ->whereUuid('media_album')
        ->whereNumber('media')
        ->scopeBindings()
        ->name('media-album.delete-media');

});
