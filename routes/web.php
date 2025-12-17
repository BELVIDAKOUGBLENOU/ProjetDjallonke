<?php

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

Route::get('/', function () {
    return view('welcome');
});

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

});
