<?php

use Illuminate\Support\Facades\Route;
use Orion\Facades\Orion;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\PostTagsController;

// Route::group(['as' => 'api.'], function () {
//     Orion::resource('posts', PostsController::class)->withSoftDeletes();
//     Orion::morphToManyResource('posts', 'tags', PostTagsController::class);
// });
