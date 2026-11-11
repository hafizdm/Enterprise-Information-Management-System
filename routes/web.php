<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\HR\EmployeeController;
use App\Http\Controllers\HR\EmployeeBusinessCardController;
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
use Illuminate\Support\Facades\Mail;

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
// Public Digital Business Card (Guest / QR Scan)
// =========================

Route::middleware('throttle:60,1')->group(function () {
    Route::get('/c/{token}', [\App\Http\Controllers\Public\PublicBusinessCardController::class, 'show'])
        ->name('public.business-card.show');

    Route::get('/c/{token}/vcf', [\App\Http\Controllers\Public\PublicBusinessCardController::class, 'downloadVcf'])
        ->name('public.business-card.vcf');
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

        
        // SPD Approval Email Confirmation
        Route::get(
            '/spd-approvals/{spd}/email-confirm/{stage}',
            [SpdApprovalController::class, 'emailConfirm']
        )
            ->middleware('signed')
            ->name('spd.approvals.email-confirm');


        // SPD Report Approval Routes

        Route::get('/spd-report-approvals', [SpdReportApprovalController::class, 'index'])
            ->name('spd-report-approvals.index');

        Route::get('/spd-report-approvals/{spdReport}', [SpdReportApprovalController::class, 'show'])
            ->name('spd-report-approvals.show');

        Route::post('/spd-report-approvals/{spdReport}/approve', [SpdReportApprovalController::class, 'approve'])
            ->name('spd-report-approvals.approve');

        Route::post('/spd-report-approvals/{spdReport}/reject', [SpdReportApprovalController::class, 'reject'])
            ->name('spd-report-approvals.reject');

        
        // SPD Report Approval Email Confirmation
        Route::get(
            '/spd-report-approvals/{spdReport}/email-confirm',
            [SpdReportApprovalController::class, 'emailConfirm']
        )
            ->middleware('signed')
            ->name('spd-report-approvals.email-confirm');


    });

    Route::get('/leave-approvals/{leaveRequest}/email-approve',[LeaveRequestController::class, 'managerApproveFromEmail'])
    ->middleware('signed')
    ->name('leave-approvals.email-approve');



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

    // Digital Business Card (Internal)
    Route::prefix('employees/{employee}/business-card')->name('employees.business-card.')->group(function () {
        Route::get('/', [EmployeeBusinessCardController::class, 'show'])->name('show');
        Route::put('/', [EmployeeBusinessCardController::class, 'update'])->name('update');
        Route::get('/qr', [EmployeeBusinessCardController::class, 'downloadQr'])->name('qr');
        Route::get('/pdf', [EmployeeBusinessCardController::class, 'downloadPdf'])->name('pdf');
    });

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

    // Travel Management - Master Vendors & Requests
    Route::prefix('travel')->name('travel.')->group(function () {
        // Central Travel Requests & Project Travel Requests
        Route::get('requests', [\App\Http\Controllers\Travel\TravelRequestController::class, 'index'])
            ->name('requests.index');
        Route::get('requests/create', [\App\Http\Controllers\Travel\TravelRequestController::class, 'create'])
            ->name('requests.create');
        Route::post('requests', [\App\Http\Controllers\Travel\TravelRequestController::class, 'store'])
            ->name('requests.store');
        Route::get('requests/{travelRequest}', [\App\Http\Controllers\Travel\TravelRequestController::class, 'show'])
            ->name('requests.show');
        Route::post('requests/{travelRequest}/cancel', [\App\Http\Controllers\Travel\TravelRequestController::class, 'cancel'])
            ->name('requests.cancel');

        // Inquiries & Quotations (Internal HR/GA HO & Site)
        Route::get('inquiries/create', [\App\Http\Controllers\Travel\TravelInquiryController::class, 'create'])
            ->name('inquiries.create');
        Route::post('inquiries', [\App\Http\Controllers\Travel\TravelInquiryController::class, 'store'])
            ->name('inquiries.store');
        Route::get('inquiries/{inquiry}', [\App\Http\Controllers\Travel\TravelInquiryController::class, 'show'])
            ->name('inquiries.show');
        Route::get('inquiries/{inquiry}/quotations/create', [\App\Http\Controllers\Travel\VendorQuotationController::class, 'create'])
            ->name('quotations.create');
        Route::post('inquiries/{inquiry}/quotations', [\App\Http\Controllers\Travel\VendorQuotationController::class, 'store'])
            ->name('quotations.store');
        Route::get('inquiries/{inquiry}/quotations/review', [\App\Http\Controllers\Travel\VendorQuotationController::class, 'review'])
            ->name('quotations.review');
        Route::post('quotations/{package}/select', [\App\Http\Controllers\Travel\VendorQuotationController::class, 'select'])
            ->name('quotations.select');

        // Confirmed Bookings & Tickets (Internal HR/GA HO)
        Route::get('bookings', [\App\Http\Controllers\Travel\TravelBookingController::class, 'index'])
            ->name('bookings.index');
        Route::get('bookings/{booking}', [\App\Http\Controllers\Travel\TravelBookingController::class, 'show'])
            ->name('bookings.show');
        Route::post('bookings/{booking}/tickets', [\App\Http\Controllers\Travel\TravelBookingController::class, 'uploadTicket'])
            ->name('bookings.tickets.store');
        Route::post('tickets/{ticket}/verify', [\App\Http\Controllers\Travel\TravelBookingController::class, 'verifyTicket'])
            ->name('tickets.verify');
        Route::get('tickets/{ticket}/download', [\App\Http\Controllers\Travel\TravelBookingController::class, 'downloadTicket'])
            ->name('tickets.download');

        Route::resource('vendors', \App\Http\Controllers\Travel\MasterVendorController::class);
        Route::post('vendors/{vendor}/activate', [\App\Http\Controllers\Travel\MasterVendorController::class, 'activate'])
            ->name('vendors.activate');
    });
});



