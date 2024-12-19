<?php

use Illuminate\Support\Facades\Route;

Route::any('/', function () {
    abort(404);
});
