<?php

use App\Http\Controllers\Api\Frontend\CommunityRemoteController;
use App\Http\Controllers\Api\Frontend\GeographicRemoteController;
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

Route::middleware('auth:sanctum')->prefix("geo")->group(function () {


    Route::get('countries', [GeographicRemoteController::class, 'countries'])->name('countries.index');
    Route::get('countries/{country}/districts', [GeographicRemoteController::class, 'districts'])->name('countries.districts');
    Route::get('districts/{district}/sub-districts', [GeographicRemoteController::class, 'subDistricts'])->name('districts.sub-districts');
    Route::get('sub-districts/{subDistrict}/villages', [GeographicRemoteController::class, 'villages'])->name('sub-districts.villages');

    Route::post('countries', [GeographicRemoteController::class, 'storeCountry'])->name('countries.store');
    Route::put('countries/{country}', [GeographicRemoteController::class, 'updateCountry'])->name('countries.update');
    Route::delete('countries/{country}', [GeographicRemoteController::class, 'destroyCountry'])->name('countries.destroy');

    Route::post('districts', [GeographicRemoteController::class, 'storeDistrict'])->name('districts.store');
    Route::put('districts/{district}', [GeographicRemoteController::class, 'updateDistrict'])->name('districts.update');
    Route::delete('districts/{district}', [GeographicRemoteController::class, 'destroyDistrict'])->name('districts.destroy');

    Route::post('sub-districts', [GeographicRemoteController::class, 'storeSubDistrict'])->name('sub-districts.store');
    Route::put('sub-districts/{subDistrict}', [GeographicRemoteController::class, 'updateSubDistrict'])->name('sub-districts.update');
    Route::delete('sub-districts/{subDistrict}', [GeographicRemoteController::class, 'destroySubDistrict'])->name('sub-districts.destroy');

    Route::post('villages', [GeographicRemoteController::class, 'storeVillage'])->name('villages.store');
    Route::put('villages/{village}', [GeographicRemoteController::class, 'updateVillage'])->name('villages.update');
    Route::delete('villages/{village}', [GeographicRemoteController::class, 'destroyVillage'])->name('villages.destroy');

});
