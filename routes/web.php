<?php

use Illuminate\Support\Facades\Route;

Route::any('/', function () {
    abort(404);
});

require __DIR__.'/auth.php';
