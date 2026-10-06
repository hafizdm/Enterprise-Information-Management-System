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
use App\Http\Controllers\HR\SpdController;
use App\Http\Controllers\HR\SpdApprovalController;
use App\Http\Controllers\Master\CostLevelController;
use App\Http\Controllers\HR\SpdReportController;
use App\Http\Controllers\HR\SpdReportApprovalController;

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

    
    // Approval Manager 
    Route::middleware('role:Employee Approval')->group(function () {

        //Leave Approval Routes

        Route::get('/leave-approvals', [LeaveRequestController::class, 'managerIndex'])
            ->name('leave-approvals.index');

        Route::get('/leave-approvals/{leaveRequest}', [LeaveRequestController::class, 'managerShow'])
            ->name('leave-approvals.show');

        Route::post('/leave-approvals/{leaveRequest}/approve', [LeaveRequestController::class, 'managerApprove'])
            ->name('leave-approvals.approve');

        Route::post('/leave-approvals/{leaveRequest}/reject', [LeaveRequestController::class, 'managerReject'])
            ->name('leave-approvals.reject');

        // SPD Approval Routes

        Route::get('/spd-approvals', [SpdApprovalController::class, 'index'])
        ->name('spd.approvals.index');

        Route::get('/spd-approvals/{spd}', [SpdApprovalController::class, 'show'])
        ->name('spd.approvals.show');

        Route::post('/spd-approvals/{spd}/approve', [SpdApprovalController::class, 'approve'])
        ->name('spd.approvals.approve');

        Route::post('/spd-approvals/{spd}/reject', [SpdApprovalController::class, 'reject'])
        ->name('spd.approvals.reject');


        // SPD Report Approval Routes

        Route::get('/spd-report-approvals', [SpdReportApprovalController::class, 'index'])
            ->name('spd-report-approvals.index');

        Route::get('/spd-report-approvals/{spdReport}', [SpdReportApprovalController::class, 'show'])
            ->name('spd-report-approvals.show');

        Route::post('/spd-report-approvals/{spdReport}/approve', [SpdReportApprovalController::class, 'approve'])
            ->name('spd-report-approvals.approve');

        Route::post('/spd-report-approvals/{spdReport}/reject', [SpdReportApprovalController::class, 'reject'])
            ->name('spd-report-approvals.reject');

    });


  // HR Monitoring - HRD ONLY
    Route::middleware('role:HRD')->group(function () {
        
        //Leave Monitoring Routes
        Route::get('/leave-monitoring', [LeaveRequestController::class, 'hrIndex'])
            ->name('leave-monitoring.index');

        Route::get('/leave-monitoring/{leaveRequest}', [LeaveRequestController::class, 'monitoringShow'])
            ->name('leave-monitoring.show');

        // SPD Report Monitoring 
        Route::get('/spd-report-monitoring', [SpdReportController::class, 'hrMonitoring']) 
            ->name('spd-report-monitoring.index');
        Route::get('/spd-report-monitoring/{spdReport}', [SpdReportController::class, 'hrMonitoringShow'])
            ->name('spd-report-monitoring.show');
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
    Route::resource('cost-levels', CostLevelController::class);

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

// SPD Report Routes
    Route::get('/spd-reports', [SpdReportController::class, 'index'])
        ->name('spd-reports.index');

    Route::get('/spd-reports/create', [SpdReportController::class, 'create'])
        ->name('spd-reports.create');

    Route::post('/spd-reports', [SpdReportController::class, 'store'])
        ->name('spd-reports.store');

    Route::get('/spd-reports/{spdReport}', [SpdReportController::class, 'show'])
        ->name('spd-reports.show');
    Route::get('/spd-reports/{spdReport}/pdf',[SpdReportController::class, 'pdf'])
        ->name('spd-reports.pdf');


// SPD Routes
    Route::middleware('auth')->group(function () {

    Route::get('/spds', [SpdController::class, 'index'])
        ->name('spds.index');

    Route::get('/spds/create', [SpdController::class, 'create'])
        ->name('spds.create');

    Route::post('/spds', [SpdController::class, 'store'])
        ->name('spds.store');

    Route::get('/spds/{spd}/pdf', [SpdController::class, 'pdf'])
        ->name('spds.pdf');

    Route::get('/spds/{spd}', [SpdController::class, 'show'])
        ->name('spds.show');

    Route::delete('/spds/{spd}', [SpdController::class, 'destroy'])
    ->name('spds.destroy');

});

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