<?php

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AnimalController;
use App\Http\Controllers\Api\PersonController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\PremiseController;
use App\Http\Controllers\Api\VillageController;
use App\Http\Middleware\SetCommunityContextAPI;
use App\Http\Controllers\Api\DistrictController;
use App\Http\Controllers\Api\CommunityController;
use App\Http\Controllers\Api\PersonRoleController;
use App\Http\Controllers\Api\HealthEventController;
use App\Http\Controllers\Api\MovementEventController;
use App\Http\Controllers\Api\TransactionEventController;
use App\Http\Controllers\Api\ReproductionEventController;
use App\Http\Controllers\Api\BirthEventController;
use App\Http\Controllers\Api\MilkRecordController;
use App\Http\Controllers\Api\DeathEventController;
use App\Http\Controllers\Api\SubDistrictController;
use App\Http\Middleware\AccessDonneesCommunautaire;
use App\Http\Controllers\Api\WeightRecordController;
use App\Http\Controllers\Api\AnimalIdentifierController;
use App\Http\Controllers\Api\PerformanceRecordController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [UserController::class, 'userInfo']);
    Route::get('/user/info', [UserController::class, 'userInfo']);

});
//FINI , ADMINISTRATION GLOBALE
Route::middleware('auth:sanctum')->group(function () {
    //PULL
    Route::get('/pull/community', [CommunityController::class, 'index']);
    Route::get('/pull/countries', [CountryController::class, 'index']);
    Route::get('/pull/persons', [PersonController::class, 'index']);
    Route::get('/pull/premises', [PremiseController::class, 'index']);
    Route::get('/pull/animals', [AnimalController::class, 'index']);
    Route::get('/pull/animals-identifiers', [AnimalIdentifierController::class, 'index']);
    Route::get('/pull/person-roles', [PersonRoleController::class, 'index']);
    Route::get('/pull/health-events', [HealthEventController::class, 'index']);
    Route::get('/pull/movement-events', [MovementEventController::class, 'index']);
    Route::get('/pull/transaction-events', [TransactionEventController::class, 'index']);
    Route::get('/pull/reproduction-events', [ReproductionEventController::class, 'index']);
    Route::get('/pull/birth-events', [BirthEventController::class, 'index']);
    Route::get('/pull/milk-records', [MilkRecordController::class, 'index']);
    Route::get('/pull/death-events', [DeathEventController::class, 'index']);
    Route::get('/pull/weight-records', [WeightRecordController::class, 'index']);
    //PUSH
    Route::get('/push/premises', [PremiseController::class, 'push']);
    Route::get('/push/animals', [AnimalController::class, 'push']);
    Route::get('/push/animals-identifiers', [AnimalIdentifierController::class, 'push']);
    Route::post('/push/persons', [PersonController::class, 'push']);
    Route::get('/push/animals-identifiers', [AnimalIdentifierController::class, 'push']);
    Route::get('/push/person-roles', [PersonRoleController::class, 'push']);
    Route::get('/push/health-events', [HealthEventController::class, 'push']);
    Route::get('/push/movement-events', [MovementEventController::class, 'push']);
    Route::get('/push/transaction-events', [TransactionEventController::class, 'push']);
    Route::get('/push/reproduction-events', [ReproductionEventController::class, 'push']);
    Route::get('/push/birth-events', [BirthEventController::class, 'push']);
    Route::get('/push/milk-records', [MilkRecordController::class, 'push']);
    Route::get('/push/death-events', [DeathEventController::class, 'push']);
    Route::get('/push/weight-records', [WeightRecordController::class, 'push']);
    // Route::apiResource('api-countries', CountryController::class)->only(['index', 'show']);
    // Route::get('get-all-countries', [CountryController::class, 'getAllData']);

    // Route::apiResource('api-districts', DistrictController::class)->only(['index', 'show']);
    // Route::get('get-all-districts', [DistrictController::class, 'getAllData']);

    // Route::apiResource('api-sub-districts', SubDistrictController::class)->only(['index', 'show']);
    // Route::get('get-all-sub-districts', [SubDistrictController::class, 'getAllData']);

    // Route::apiResource('api-villages', VillageController::class)->only(['index', 'show']);
    // Route::get('get-all-villages', [VillageController::class, 'getAllData']);
    // Route::apiResource('api-communities', CommunityController::class)->only(['index', 'show']);
    // Route::get('get-all-communities', [CommunityController::class, 'getAllData']);

});

// Route::middleware(['auth:sanctum', SetCommunityContextAPI::class])->group(function () {


//     Route::apiResource('api-persons', PersonController::class)->only(['index',]);

//     Route::get('get-all-persons', [PersonController::class, 'getAllData']);


//     Route::get('get-all-animals', [AnimalController::class, 'getAllData']);

//     Route::apiResource('api-premises', PremiseController::class);
//     Route::get('get-all-premises', [PremiseController::class, 'getAllData']);
//     Route::post('sync-premises', [PremiseController::class, 'syncPremises']);
//     // RESTE A FAIR
//     Route::apiResource('api-animals', AnimalController::class);
//     // add identifier
// // remove identifier
// //edit identifier
// });
// reste a faire
// Route::middleware('auth:sanctum')->group(function () {


//     Route::apiResource('api-performance-records', PerformanceRecordController::class);
//     Route::get('get-all-performance-records', [PerformanceRecordController::class, 'getAllData']);

//     Route::apiResource('api-weight-records', WeightRecordController::class);
//     Route::get('get-all-weight-records', [WeightRecordController::class, 'getAllData']);

// });
