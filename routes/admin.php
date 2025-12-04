<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\EventOrganizerController;
use App\Http\Controllers\Admin\RegistrationApprovalController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    // Guest routes
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AuthController::class, 'login']);
    });

    // Authenticated routes
    Route::middleware('auth:admin')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // Event Organizer Management
        Route::resource('organizers', EventOrganizerController::class);
        Route::post('organizers/{organizer}/approve', [EventOrganizerController::class, 'approve'])->name('organizers.approve');
        Route::post('organizers/{organizer}/reject', [EventOrganizerController::class, 'reject'])->name('organizers.reject');

        // Registration Approvals
        Route::prefix('approvals')->name('approvals.')->group(function () {
            Route::get('/', [RegistrationApprovalController::class, 'index'])->name('index');
            Route::get('/{registration}', [RegistrationApprovalController::class, 'show'])->name('show');
            Route::post('/{registration}/approve', [RegistrationApprovalController::class, 'approve'])->name('approve');
            Route::post('/{registration}/reject', [RegistrationApprovalController::class, 'reject'])->name('reject');
            Route::post('/bulk-approve', [RegistrationApprovalController::class, 'bulkApprove'])->name('bulk-approve');
            Route::post('/bulk-reject', [RegistrationApprovalController::class, 'bulkReject'])->name('bulk-reject');
        });
    });
});