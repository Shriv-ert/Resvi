<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\FacilityController as AdminFacilityController;
use App\Http\Controllers\Admin\RecapController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\FacilityController as PublicFacilityController;
use App\Http\Controllers\ReservationFlowController;
use App\Http\Controllers\Staff\FacilityController as StaffFacilityController;
use App\Http\Controllers\Staff\ReportController as StaffReportController;
use App\Http\Controllers\Staff\ReservationController as StaffReservationController;
use App\Http\Controllers\User\ReportController as UserReportController;
use App\Http\Controllers\User\ReservationController as UserReservationController;
use Illuminate\Support\Facades\Route;

// -------------------------------------------------------------
// Public & Shared Routes (Pengunjung & Pengguna)
// -------------------------------------------------------------

Route::get('/', [PublicFacilityController::class, 'index'])->name('home');
Route::get('/facilities', [PublicFacilityController::class, 'index'])->name('facilities.index');
Route::get('/facilities/{facility}', [PublicFacilityController::class, 'show'])->name('facilities.show');

Route::get('/reservations/create', [ReservationFlowController::class, 'create'])->name('reservations.create');
Route::post('/reservations/check-availability', [ReservationFlowController::class, 'checkAvailability'])->name('reservations.check_availability');
Route::post('/reservations/draft', [ReservationFlowController::class, 'storeDraft'])->middleware('guest')->name('reservations.store_draft');

// Auth routes
Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');


// -------------------------------------------------------------
// Pengguna (User) Routes (SRS FR-PGN)
// -------------------------------------------------------------

Route::middleware(['auth', 'role:pengguna'])->prefix('my')->group(function (): void {
    // Riwayat & Pengajuan Reservasi Pengguna
    Route::get('/reservations', [UserReservationController::class, 'index'])->name('user.reservations.index');
    Route::post('/reservations', [UserReservationController::class, 'store'])->name('user.reservations.store');
    Route::get('/reservations/{reservation}', [UserReservationController::class, 'show'])->name('user.reservations.show');
    Route::post('/reservations/{reservation}/cancel', [UserReservationController::class, 'cancel'])->name('user.reservations.cancel');

    // Riwayat & Pelaporan Kerusakan Pengguna
    Route::get('/reports', [UserReportController::class, 'index'])->name('user.reports.index');
    Route::get('/reports/create', [UserReportController::class, 'create'])->name('user.reports.create');
    Route::post('/reports', [UserReportController::class, 'store'])->name('user.reports.store');
    Route::get('/reports/{report}', [UserReportController::class, 'show'])->name('user.reports.show');
});


// -------------------------------------------------------------
// Petugas (Staff) Routes (SRS FR-PTG)
// -------------------------------------------------------------

Route::middleware(['auth', 'role:petugas'])->prefix('staff')->name('staff.')->group(function (): void {
    // Antrian & Riwayat Reservasi Petugas
    Route::get('/reservations', [StaffReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservations/history', [StaffReservationController::class, 'history'])->name('reservations.history');
    Route::get('/reservations/{reservation}', [StaffReservationController::class, 'show'])->name('reservations.show');
    Route::post('/reservations/{reservation}/approve', [StaffReservationController::class, 'approve'])->name('reservations.approve');
    Route::post('/reservations/{reservation}/reject', [StaffReservationController::class, 'reject'])->name('reservations.reject');
    Route::post('/reservations/{reservation}/cancel', [StaffReservationController::class, 'cancel'])->name('reservations.cancel');

    // Antrian & Riwayat Laporan Petugas
    Route::get('/reports', [StaffReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/history', [StaffReportController::class, 'history'])->name('reports.history');
    Route::get('/reports/{report}', [StaffReportController::class, 'show'])->name('reports.show');
    Route::post('/reports/{report}/start-process', [StaffReportController::class, 'startProcess'])->name('reports.start-process');
    Route::post('/reports/{report}/resolve', [StaffReportController::class, 'resolve'])->name('reports.resolve');
    Route::post('/reports/{report}/reject', [StaffReportController::class, 'reject'])->name('reports.reject');

    // Status Fasilitas & Dalam Perbaikan
    Route::get('/facilities', [StaffFacilityController::class, 'index'])->name('facilities.index');
    Route::get('/facilities/maintenance', [StaffFacilityController::class, 'maintenance'])->name('facilities.maintenance');
    Route::get('/facilities/{facility}', [StaffFacilityController::class, 'show'])->name('facilities.show');
    Route::post('/facilities/{facility}/update-status', [StaffFacilityController::class, 'updateStatus'])->name('facilities.update-status');
});


// -------------------------------------------------------------
// Administrator Routes (SRS FR-ADM)
// -------------------------------------------------------------

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
