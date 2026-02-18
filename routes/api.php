<?php

use App\Http\Controllers\Api\CommunityController;
use App\Http\Controllers\Api\Frontend\ConstantDataController;
use App\Http\Controllers\Api\Syncing\AnimalIdentifierSyncController;
use App\Http\Controllers\Api\Syncing\AnimalSyncController;
use App\Http\Controllers\Api\Syncing\BirthEventSyncController;
use App\Http\Controllers\Api\Syncing\CommunitySyncController;
use App\Http\Controllers\Api\Syncing\CountrySyncController;
use App\Http\Controllers\Api\Syncing\DeathEventSyncController;
use App\Http\Controllers\Api\Syncing\DistrictSyncController;
use App\Http\Controllers\Api\Syncing\HealthEventSyncController;
use App\Http\Controllers\Api\Syncing\MilkRecordSyncController;
use App\Http\Controllers\Api\Syncing\MovementEventSyncController;
use App\Http\Controllers\Api\Syncing\PerformanceRecordSyncController;
use App\Http\Controllers\Api\Syncing\PersonRoleSyncController;
use App\Http\Controllers\Api\Syncing\PersonSyncController;
use App\Http\Controllers\Api\Syncing\PremiseSyncController;
use App\Http\Controllers\Api\Syncing\ReproductionEventSyncController;
use App\Http\Controllers\Api\Syncing\SubDistrictSyncController;
use App\Http\Controllers\Api\Syncing\TransactionEventSyncController;
use App\Http\Controllers\Api\Syncing\UserSyncController;
use App\Http\Controllers\Api\Syncing\VillageSyncController;
use App\Http\Controllers\Api\Syncing\WeightRecordSyncController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\AccessDonneesCommunautaire;
use App\Http\Middleware\SetCommunityContextAPI;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Constants;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::get('/constants', [ConstantDataController::class, 'getConstants']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [UserSyncController::class, 'userInfo']);

    // Profile Management
    Route::get('/profile', [App\Http\Controllers\Api\Syncing\ProfileSyncController::class, 'show']);
    Route::patch('/profile', [App\Http\Controllers\Api\Syncing\ProfileSyncController::class, 'update']);
    Route::put('/profile/password', [App\Http\Controllers\Api\Syncing\ProfileSyncController::class, 'updatePassword']);
    Route::delete('/profile', [App\Http\Controllers\Api\Syncing\ProfileSyncController::class, 'destroy']);

    // Notifications
    Route::get('/notifications', [App\Http\Controllers\Api\Syncing\NotificationSyncController::class, 'pull']);
    Route::post('/notifications/read-all', [App\Http\Controllers\Api\Syncing\NotificationSyncController::class, 'markAllAsRead']);
    Route::get('/notifications/{id}', [App\Http\Controllers\Api\Syncing\NotificationSyncController::class, 'show']);

    // Dashboard
    Route::get('/dashboard/super-admin', [\App\Http\Controllers\DashboardController::class, 'superAdminStats']);
    Route::get('/dashboard/community', [\App\Http\Controllers\DashboardController::class, 'communityStats']);
    Route::patch('/notifications/{id}/read', [App\Http\Controllers\Api\Syncing\NotificationSyncController::class, 'markAsRead']);



    // // Communities (Orion)
    // Route::get('/communities/my-communities', [App\Http\Controllers\Api\CommunityController::class, 'myCommunities']); // Custom endpoint if needed distinct from index
});

Route::middleware('auth:sanctum')->group(function () {
    //PULL
    Route::get('/pull/community', [CommunitySyncController::class, 'pull']);
    Route::get('/pull/countries', [CountrySyncController::class, 'pull']);
    Route::get('/pull/persons', [PersonSyncController::class, 'pull']);
    Route::get('/pull/premises', [PremiseSyncController::class, 'pull']);
    Route::get('/pull/animals', [AnimalSyncController::class, 'pull']);
    Route::get('/pull/animals-identifiers', [AnimalIdentifierSyncController::class, 'pull']);
    Route::get('/pull/person-roles', [PersonRoleSyncController::class, 'pull']);
    Route::get('/pull/health-events', [HealthEventSyncController::class, 'pull']);
    Route::get('/pull/movement-events', [MovementEventSyncController::class, 'pull']);
    Route::get('/pull/transaction-events', [TransactionEventSyncController::class, 'pull']);
    Route::get('/pull/reproduction-events', [ReproductionEventSyncController::class, 'pull']);
    Route::get('/pull/birth-events', [BirthEventSyncController::class, 'pull']);
    Route::get('/pull/milk-records', [MilkRecordSyncController::class, 'pull']);
    Route::get('/pull/death-events', [DeathEventSyncController::class, 'pull']);
    Route::get('/pull/weight-records', [WeightRecordSyncController::class, 'pull']);
    //PUSH
    Route::post('/push/premises', [PremiseSyncController::class, 'push']);
    Route::post('/push/animals', [AnimalSyncController::class, 'push']);
    Route::post('/push/animals-identifiers', [AnimalIdentifierSyncController::class, 'push']);
    Route::post('/push/persons', [PersonSyncController::class, 'push']);
    Route::post('/push/animals-identifiers', [AnimalIdentifierSyncController::class, 'push']);
    Route::post('/push/person-roles', [PersonRoleSyncController::class, 'push']);
    Route::post('/push/health-events', [HealthEventSyncController::class, 'push']);
    Route::post('/push/movement-events', [MovementEventSyncController::class, 'push']);
    Route::post('/push/transaction-events', [TransactionEventSyncController::class, 'push']);
    Route::post('/push/reproduction-events', [ReproductionEventSyncController::class, 'push']);
    Route::post('/push/birth-events', [BirthEventSyncController::class, 'push']);
    Route::post('/push/milk-records', [MilkRecordSyncController::class, 'push']);
    Route::post('/push/death-events', [DeathEventSyncController::class, 'push']);
    Route::post('/push/weight-records', [WeightRecordSyncController::class, 'push']);
    // Route::apiResource('api-countries', CountryController::class)->only(['index', 'show']);
    // Route::get('get-all-countries', [CountrySyncController::class, 'getAllData']);

    // Route::apiResource('api-districts', DistrictController::class)->only(['index', 'show']);
    // Route::get('get-all-districts', [DistrictSyncController::class, 'getAllData']);

    // Route::apiResource('api-sub-districts', SubDistrictController::class)->only(['index', 'show']);
    // Route::get('get-all-sub-districts', [SubDistrictSyncController::class, 'getAllData']);

    // Route::apiResource('api-villages', VillageController::class)->only(['index', 'show']);
    // Route::get('get-all-villages', [VillageSyncController::class, 'getAllData']);
    // Route::apiResource('api-communities', CommunityController::class)->only(['index', 'show']);
    // Route::get('get-all-communities', [CommunityController::class, 'getAllData']);



});

require __DIR__ . '/frontend.php';
