<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\FacilityController;

use App\Http\Controllers\ReservationFlowController;

Route::get('/', [FacilityController::class, 'index'])->name('home');
Route::get('/facilities', [FacilityController::class, 'index'])->name('facilities.index');
Route::get('/facilities/{facility}', [FacilityController::class, 'show'])->name('facilities.show');

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
