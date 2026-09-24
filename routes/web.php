<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\FacilityController as AdminFacilityController;
use App\Http\Controllers\Admin\RecapController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\FacilityController as PublicFacilityController;
use App\Http\Controllers\ReservationFlowController;

Route::get('/', [PublicFacilityController::class, 'index'])->name('home');
Route::get('/facilities', [PublicFacilityController::class, 'index'])->name('facilities.index');
Route::get('/facilities/{facility}', [PublicFacilityController::class, 'show'])->name('facilities.show');

Route::get('/reservations/create', [ReservationFlowController::class, 'create'])->name('reservations.create');
Route::post('/reservations/check-availability', [ReservationFlowController::class, 'checkAvailability'])->name('reservations.check_availability');
Route::post('/reservations/draft', [ReservationFlowController::class, 'storeDraft'])->middleware('guest')->name('reservations.store_draft');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', fn () => redirect()->route('admin.facilities.index'));

    Route::get('/facilities', [AdminFacilityController::class, 'index'])->name('facilities.index');
    Route::get('/facilities/create', [AdminFacilityController::class, 'create'])->name('facilities.create');
    Route::post('/facilities', [AdminFacilityController::class, 'store'])->name('facilities.store');
    Route::get('/facilities/{facility}', [AdminFacilityController::class, 'show'])->name('facilities.show');
    Route::get('/facilities/{facility}/edit', [AdminFacilityController::class, 'edit'])->name('facilities.edit');
    Route::put('/facilities/{facility}', [AdminFacilityController::class, 'update'])->name('facilities.update');
    Route::post('/facilities/{facility}/deactivate', [AdminFacilityController::class, 'deactivate'])->name('facilities.deactivate');
    Route::post('/facilities/{facility}/reactivate', [AdminFacilityController::class, 'reactivate'])->name('facilities.reactivate');

    Route::get('/accounts', [AccountController::class, 'index'])->name('accounts.index');
    Route::get('/accounts/create', [AccountController::class, 'create'])->name('accounts.create');
    Route::post('/accounts', [AccountController::class, 'store'])->name('accounts.store');
    Route::get('/accounts/created', [AccountController::class, 'created'])->name('accounts.created');
    Route::get('/accounts/{user}', [AccountController::class, 'show'])->name('accounts.show');
    Route::post('/accounts/{user}/reset-password', [AccountController::class, 'resetPassword'])->name('accounts.reset-password');

    Route::get('/recaps', [RecapController::class, 'index'])->name('recaps.index');
    Route::get('/recaps/export', [RecapController::class, 'export'])->name('recaps.export');
});
