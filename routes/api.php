<?php

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\AnimalController;
use App\Http\Controllers\Api\PersonController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\PremiseController;
use App\Http\Controllers\Api\VillageController;
use App\Http\Controllers\Api\DistrictController;
use App\Http\Controllers\Api\CommunityController;
use App\Http\Controllers\Api\SubDistrictController;
use App\Http\Middleware\AccessDonneesCommunautaire;
use App\Http\Controllers\Api\WeightRecordController;
use App\Http\Controllers\Api\PerformanceRecordController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return UserResource::make($request->user());
    });
});
//FINI , ADMINISTRATION GLOBALE
Route::middleware('auth:sanctum')->group(function () {

    Route::apiResource('countries', CountryController::class);
    Route::get('get-all-countries', [CountryController::class, 'getAllData']);

    Route::apiResource('districts', DistrictController::class);
    Route::get('get-all-districts', [DistrictController::class, 'getAllData']);

    Route::apiResource('sub-districts', SubDistrictController::class);
    Route::get('get-all-sub-districts', [SubDistrictController::class, 'getAllData']);

    Route::apiResource('villages', VillageController::class);
    Route::get('get-all-villages', [VillageController::class, 'getAllData']);
    Route::apiResource('communities', CommunityController::class);
    Route::get('get-all-communities', [CommunityController::class, 'getAllData']);

});
//MEMBRE DE COMMUNAUTE
// rest a faire
Route::middleware(['auth:sanctum', AccessDonneesCommunautaire::class])->group(function () {

    Route::apiResource('persons', PersonController::class);
    Route::get('get-all-persons', [PersonController::class, 'getAllData']);
    Route::apiResource('animals', AnimalController::class);
    Route::get('get-all-animals', [AnimalController::class, 'getAllData']);

});
Route::middleware('auth:sanctum')->group(function () {



    Route::apiResource('premises', PremiseController::class);
    Route::get('get-all-premises', [PremiseController::class, 'getAllData']);

    Route::apiResource('performance-records', PerformanceRecordController::class);
    Route::get('get-all-performance-records', [PerformanceRecordController::class, 'getAllData']);

    Route::apiResource('weight-records', WeightRecordController::class);
    Route::get('get-all-weight-records', [WeightRecordController::class, 'getAllData']);

});
