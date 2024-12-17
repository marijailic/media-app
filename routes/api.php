<?php

use App\Http\Controllers\Api\MediaAlbumController;
use Illuminate\Support\Facades\Route;

Route::apiResource('media-album', MediaAlbumController::class)->except(['update']);

