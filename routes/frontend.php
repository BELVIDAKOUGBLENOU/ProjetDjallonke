<?php

use App\Http\Controllers\Api\Frontend\CommunityRemoteController;
use Illuminate\Support\Facades\Route;
use Orion\Facades\Orion;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\PostTagsController;

// Route::group(['as' => 'api.'], function () {
//     Orion::resource('posts', PostsController::class)->withSoftDeletes();
//     Orion::morphToManyResource('posts', 'tags', PostTagsController::class);
// });


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/communities/my-communities', [CommunityRemoteController::class, 'myCommunities']);

    // Member management routes
    Route::post('/communities/{community}/members', [CommunityRemoteController::class, 'addMember']);
    Route::put('/communities/{community}/members/{userId}', [CommunityRemoteController::class, 'updateMember']);
    Route::delete('/communities/{community}/members/{userId}', [CommunityRemoteController::class, 'removeMember']);

    Route::apiResource('fr-communities', CommunityRemoteController::class);
    Route::apiResource('fr-countries', \App\Http\Controllers\Api\Frontend\CountryRemoteController::class);
    Route::apiResource('fr-roles', \App\Http\Controllers\Api\Frontend\RoleRemoteController::class);
    Route::post('fr-roles/{role}/permissions', [\App\Http\Controllers\Api\Frontend\RoleRemoteController::class, 'updatePermissions']);

    Route::apiResource('fr-users', \App\Http\Controllers\Api\Frontend\UserRemoteController::class);
    // Orion::resource('communities', App\Http\Controllers\Api\Orion\CommunityControllerOrion::class);

    Route::get('fr-premises-export', [\App\Http\Controllers\Api\Frontend\PremiseController::class, 'export']);
    Route::apiResource('fr-premises', \App\Http\Controllers\Api\Frontend\PremiseController::class);

    Route::get('fr-persons-export', [\App\Http\Controllers\Api\Frontend\PersonRemoteController::class, 'export']);
    Route::apiResource('fr-persons', \App\Http\Controllers\Api\Frontend\PersonRemoteController::class)->parameters(['fr-persons' => 'person']);
    Route::get('fr-animals-export', [\App\Http\Controllers\Api\Frontend\AnimalRemoteController::class, 'export']);
    Route::apiResource('fr-animals', \App\Http\Controllers\Api\Frontend\AnimalRemoteController::class)->parameters(['fr-animals' => 'animal']);
    Route::get('fr-events/statistics', [\App\Http\Controllers\Api\Frontend\EventRemoteController::class, 'statistics']);
    Route::get('fr-events-export', [\App\Http\Controllers\Api\Frontend\EventRemoteController::class, 'export']);
    Route::apiResource('fr-events', \App\Http\Controllers\Api\Frontend\EventRemoteController::class)->parameters(['fr-events' => 'event'])->except(['store']);

});
