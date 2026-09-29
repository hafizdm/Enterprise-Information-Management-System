<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\HR\EmployeeController;
use App\Http\Controllers\Master\DivisionController;
use App\Http\Controllers\Master\PositionController;
use App\Http\Controllers\Master\ProjectController;
use App\Http\Controllers\LeaveApprovalSettingController;
use App\Http\Controllers\HR\LeaveRequestController;

// =========================
// Guest
// =========================

Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.submit');
});


// =========================
// Authentication
// =========================

Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    // Change Password
    Route::get('/password/change', function () {
        return view('auth.change-password');
    })->name('password.change');

    Route::post('/password/change', [AuthController::class, 'changePassword'])
        ->name('password.change.update');

    
    // Approval Manager Leave Request
    Route::middleware('role:Employee Approval')->group(function () {

        Route::get('/leave-approvals', [LeaveRequestController::class, 'managerIndex'])
            ->name('leave-approvals.index');

        Route::get('/leave-approvals/{leaveRequest}', [LeaveRequestController::class, 'managerShow'])
            ->name('leave-approvals.show');

        Route::post('/leave-approvals/{leaveRequest}/approve', [LeaveRequestController::class, 'managerApprove'])
            ->name('leave-approvals.approve');

        Route::post('/leave-approvals/{leaveRequest}/reject', [LeaveRequestController::class, 'managerReject'])
            ->name('leave-approvals.reject');

    });


  // HR Leave Monitoring - HRD ONLY
    Route::middleware('role:HRD')->group(function () {

        Route::get('/leave-monitoring', [LeaveRequestController::class, 'hrIndex'])
            ->name('leave-monitoring.index');

        Route::get('/leave-monitoring/{leaveRequest}', [LeaveRequestController::class, 'monitoringShow'])
            ->name('leave-monitoring.show');

    });
});


// =========================
// EIMS Application
// =========================

Route::middleware(['auth', 'force.password.change'])->group(function () {

    // Dashboard
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');


// Master
Route::middleware('role:System Administrator')->group(function () {

    Route::resource('divisions', DivisionController::class);
    Route::resource('positions', PositionController::class);
    Route::resource('projects', ProjectController::class);

});


    // HR
    Route::resource('employees', EmployeeController::class);

    Route::get('/leave-requests',[LeaveRequestController::class, 'index'])
    ->name('leave-requests.index');

    Route::get('/leave-requests/create',[LeaveRequestController::class, 'create'])
    ->name('leave-requests.create');

    Route::post('/leave-requests',[LeaveRequestController::class, 'store'])
    ->name('leave-requests.store');

    Route::get('/leave-requests/{leaveRequest}/pdf', [LeaveRequestController::class, 'pdf'])
    ->name('leave-requests.pdf');

    Route::get('/leave-requests/{leaveRequest}', [LeaveRequestController::class, 'show'])
    ->name('leave-requests.show');

    Route::delete('/leave-requests/{leaveRequest}', [LeaveRequestController::class, 'destroy'])
    ->name('leave-requests.destroy');

    // User Management
    Route::resource('users', UserController::class);

    Route::get(
        '/leave-approval-settings',
        [LeaveApprovalSettingController::class, 'edit']
        )->name('leave-approval-settings.edit');

    Route::put(
        '/leave-approval-settings',
        [LeaveApprovalSettingController::class, 'update']
        )->name('leave-approval-settings.update');
});