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

    Route::apiResource('fr-premises', \App\Http\Controllers\Api\Frontend\PremiseController::class);
    // Route::get('villages', [\App\Http\Controllers\Api\Frontend\VillageController::class, 'index']);
    Route::prefix('geo')->group(function () {
        // Tree Loading Endpoints (using Remote Controllers which return resources)
        // Note: For Tree View, we might need 'all' instead of paginated.
        // Adapting Store calls to handle pagination or requesting all?
        // For now, let's use the RemoteControllers but ideally index methods should support 'all=true'

        Route::get('/countries', [\App\Http\Controllers\Api\Frontend\CountryRemoteController::class, 'index']);
        Route::get('/countries/{country}/districts', [\App\Http\Controllers\Api\Frontend\DistrictRemoteController::class, 'indexByCountry']);
        Route::get('/districts/{district}/sub-districts', [\App\Http\Controllers\Api\Frontend\SubDistrictRemoteController::class, 'indexByDistrict']);
        Route::get('/sub-districts/{subDistrict}/villages', [\App\Http\Controllers\Api\Frontend\VillageRemoteController::class, 'indexBySubDistrict']);

        // CRUD Routes
        // Note: fr-countries is also defined in frontend.php but we need it here under /geo prefix for the new Geographic module generic CRUD
        // Route::apiResource('fr-countries', \App\Http\Controllers\Api\Frontend\CountryRemoteController::class);
        Route::apiResource('districts', \App\Http\Controllers\Api\Frontend\DistrictRemoteController::class);
        Route::apiResource('sub-districts', \App\Http\Controllers\Api\Frontend\SubDistrictRemoteController::class);
        Route::apiResource('villages', \App\Http\Controllers\Api\Frontend\VillageRemoteController::class);
    });

    Route::apiResource('fr-persons', \App\Http\Controllers\Api\Frontend\PersonRemoteController::class)->parameters(['fr-persons' => 'person']);
    Route::apiResource('fr-animals', \App\Http\Controllers\Api\Frontend\AnimalRemoteController::class)->parameters(['fr-animals' => 'animal']);
    Route::get('fr-events/statistics', [\App\Http\Controllers\Api\Frontend\EventRemoteController::class, 'statistics']);
    Route::apiResource('fr-events', \App\Http\Controllers\Api\Frontend\EventRemoteController::class)->parameters(['fr-events' => 'event'])->except(['store']);

});
