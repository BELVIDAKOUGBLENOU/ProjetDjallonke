<?php

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AnimalController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\PremiseController;
use App\Http\Controllers\VillageController;
use App\Http\Controllers\DistrictController;
use App\Http\Middleware\SetCommunityContext;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\SubDistrictController;
use App\Http\Controllers\GeographicManagementController;

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes(['register' => false]);

Route::middleware(["auth", SetCommunityContext::class])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [App\Http\Controllers\ProfileController::class, 'password'])->name('profile.password');
    Route::delete('/profile', [App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('users/{user}/impersonate', [UserController::class, 'impersonate'])->name('users.impersonate');
    Route::get('users-stop-impersonate', [UserController::class, 'stopImpersonate'])->name('users.stop-impersonate');
    Route::resource('users', UserController::class);


    Route::resource('roles', App\Http\Controllers\RoleController::class);
    Route::post('roles/{role}/permissions', [App\Http\Controllers\RoleController::class, 'updatePermissions'])->name('roles.permissions.update');

    // Notifications
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}', [App\Http\Controllers\NotificationController::class, 'show'])->name('notifications.show');
    Route::post('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
    //

    Route::get('geographic-management', [GeographicManagementController::class, 'index'])->name('geographic-management.index');

    Route::resource('countries', CountryController::class);
    Route::resource('districts', DistrictController::class);
    Route::resource('sub-districts', SubDistrictController::class);
    Route::resource('villages', VillageController::class);

    Route::resource('communities', CommunityController::class);
    Route::get('my-communities', [CommunityController::class, 'myCommunities'])->name('my-communities');
    Route::post('select-community', [CommunityController::class, 'selectCommunity'])->name('select-community');

    Route::post('communities/{community}/members', [App\Http\Controllers\CommunityMembershipController::class, 'store'])->name('communities.members.store');
    Route::put('communities/{community}/members/{user}', [App\Http\Controllers\CommunityMembershipController::class, 'update'])->name('communities.members.update');
    Route::delete('communities/{community}/members/{user}', [App\Http\Controllers\CommunityMembershipController::class, 'destroy'])->name('communities.members.destroy');
    Route::get('premises/export', [PremiseController::class, 'export'])->name('premises.export');
    Route::get('premises/template', [PremiseController::class, 'downloadTemplate'])->name('premises.template');
    Route::post('premises/import', [PremiseController::class, 'upload'])->name('premises.import');
    Route::get('premises/import/preview', [PremiseController::class, 'preview'])->name('premises.import.preview');
    Route::post('premises/import/confirm', [PremiseController::class, 'confirmImport'])->name('premises.import.confirm');
    Route::get('premises/import/errors', [PremiseController::class, 'downloadErrors'])->name('premises.import.errors');
    Route::resource('premises', PremiseController::class);

    Route::post('premises/{premise}/keepers', [App\Http\Controllers\PremisesKeeperController::class, 'store'])->name('premises.keepers.store');
    Route::put('premises-keepers/{premisesKeeper}', [App\Http\Controllers\PremisesKeeperController::class, 'update'])->name('premises-keepers.update');
    Route::delete('premises-keepers/{premisesKeeper}', [App\Http\Controllers\PremisesKeeperController::class, 'destroy'])->name('premises-keepers.destroy');


    Route::get('people/export', [PersonController::class, 'export'])->name('people.export');
    Route::get('people/template', [PersonController::class, 'downloadTemplate'])->name('people.template');
    Route::post('people/import', [PersonController::class, 'upload'])->name('people.import');
    Route::get('people/import/preview', [PersonController::class, 'preview'])->name('people.import.preview');
    Route::post('people/import/confirm', [PersonController::class, 'confirmImport'])->name('people.import.confirm');
    Route::get('people/import/errors', [PersonController::class, 'downloadErrors'])->name('people.import.errors');
    Route::resource('people', PersonController::class);

    Route::post('animals/{animal}/identifiers', [App\Http\Controllers\AnimalIdentifierController::class, 'store'])->name('animals.identifiers.store');
    Route::put('animal-identifiers/{animalIdentifier}', [App\Http\Controllers\AnimalIdentifierController::class, 'update'])->name('animal-identifiers.update');
    Route::delete('animal-identifiers/{animalIdentifier}', [App\Http\Controllers\AnimalIdentifierController::class, 'destroy'])->name('animal-identifiers.destroy');
    Route::get('animals/export', [AnimalController::class, 'export'])->name('animals.export');
    Route::get('animals/template', [AnimalController::class, 'downloadTemplate'])->name('animals.template');
    Route::post('animals/import', [AnimalController::class, 'upload'])->name('animals.import');
    Route::get('animals/import/preview', [AnimalController::class, 'preview'])->name('animals.import.preview');
    Route::post('animals/import/confirm', [AnimalController::class, 'confirmImport'])->name('animals.import.confirm');
    Route::get('animals/import/errors', [AnimalController::class, 'downloadErrors'])->name('animals.import.errors');
    Route::resource('animals', AnimalController::class);

    // API Geographic Routes (Session Auth)
    Route::middleware(['auth'])->prefix('api/geo')->name('api.geo.')->group(function () {
        Route::get('countries', [App\Http\Controllers\Api\GeographicApiController::class, 'countries'])->name('countries.index');
        Route::get('countries/{country}/districts', [App\Http\Controllers\Api\GeographicApiController::class, 'districts'])->name('countries.districts');
        Route::get('districts/{district}/sub-districts', [App\Http\Controllers\Api\GeographicApiController::class, 'subDistricts'])->name('districts.sub-districts');
        Route::get('sub-districts/{subDistrict}/villages', [App\Http\Controllers\Api\GeographicApiController::class, 'villages'])->name('sub-districts.villages');

        Route::post('countries', [App\Http\Controllers\Api\GeographicApiController::class, 'storeCountry'])->name('countries.store');
        Route::put('countries/{country}', [App\Http\Controllers\Api\GeographicApiController::class, 'updateCountry'])->name('countries.update');
        Route::delete('countries/{country}', [App\Http\Controllers\Api\GeographicApiController::class, 'destroyCountry'])->name('countries.destroy');

        Route::post('districts', [App\Http\Controllers\Api\GeographicApiController::class, 'storeDistrict'])->name('districts.store');
        Route::put('districts/{district}', [App\Http\Controllers\Api\GeographicApiController::class, 'updateDistrict'])->name('districts.update');
        Route::delete('districts/{district}', [App\Http\Controllers\Api\GeographicApiController::class, 'destroyDistrict'])->name('districts.destroy');

        Route::post('sub-districts', [App\Http\Controllers\Api\GeographicApiController::class, 'storeSubDistrict'])->name('sub-districts.store');
        Route::put('sub-districts/{subDistrict}', [App\Http\Controllers\Api\GeographicApiController::class, 'updateSubDistrict'])->name('sub-districts.update');
        Route::delete('sub-districts/{subDistrict}', [App\Http\Controllers\Api\GeographicApiController::class, 'destroySubDistrict'])->name('sub-districts.destroy');

        Route::post('villages', [App\Http\Controllers\Api\GeographicApiController::class, 'storeVillage'])->name('villages.store');
        Route::put('villages/{village}', [App\Http\Controllers\Api\GeographicApiController::class, 'updateVillage'])->name('villages.update');
        Route::delete('villages/{village}', [App\Http\Controllers\Api\GeographicApiController::class, 'destroyVillage'])->name('villages.destroy');
    });

});
Route::redirect('/', '/home');


Route::get("/api/update", [Controller::class, "autoUpdate"])->name("update")->middleware(['auth', SetCommunityContext::class, 'role:Super-admin']);
