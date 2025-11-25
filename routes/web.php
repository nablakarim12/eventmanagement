<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\EventCategoryController;
use App\Http\Controllers\Admin\EventOrganizerController;
use App\Http\Controllers\Organizer\AuthController as OrganizerAuthController;
use App\Http\Controllers\Organizer\DashboardController as OrganizerDashboardController;
use App\Http\Controllers\Organizer\EventController;
use App\Http\Controllers\Organizer\RegistrationController;
use App\Http\Controllers\Organizer\AnalyticsController;
use App\Http\Controllers\Organizer\ApprovalController as OrganizerApprovalController;
use App\Http\Controllers\PublicEventController;
use App\Http\Controllers\EventRegistrationController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', function () {
    return redirect('/');
});

// Public Event Routes
Route::prefix('events')->name('events.')->group(function () {
    Route::get('/', [PublicEventController::class, 'index'])->name('index');
    Route::get('/category/{categorySlug}', [PublicEventController::class, 'byCategory'])->name('category');
    Route::get('/{slug}', [PublicEventController::class, 'show'])->name('show');
});

Route::get('/events/search', [PublicEventController::class, 'search'])->name('events.search');

// Public QR Scanning Routes (for users scanning QR codes)
Route::prefix('scan')->name('qr.')->group(function () {
    Route::get('/{qrCode}', [App\Http\Controllers\PublicQrScanController::class, 'scan'])->name('scan');
    Route::post('/attendance', [App\Http\Controllers\PublicQrScanController::class, 'processAttendance'])->name('attendance');
});

// Registration QR Check-in Routes
Route::prefix('check-in')->name('qr.scan.')->group(function () {
    Route::get('/{qrCode}', [App\Http\Controllers\QrCheckInController::class, 'scan'])->name('registration');
    Route::post('/{qrCode}', [App\Http\Controllers\QrCheckInController::class, 'checkIn'])->name('process');
});

// User Authentication Routes - Manual implementation
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);

// User Dashboard Routes (Protected)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/profile', [DashboardController::class, 'profile'])->name('dashboard.profile');
    Route::patch('/dashboard/profile', [DashboardController::class, 'updateProfile'])->name('dashboard.profile.update');
    
    // Event Registration Routes
    Route::get('/events/{event}/register', [EventRegistrationController::class, 'create'])->name('events.register');
    Route::post('/events/{event}/register', [EventRegistrationController::class, 'store'])->name('events.register.store');
    
    // Registration Management
    Route::get('/dashboard/registrations', [EventRegistrationController::class, 'index'])->name('dashboard.registrations');
    Route::get('/dashboard/registrations/{registration}', [EventRegistrationController::class, 'show'])->name('dashboard.registrations.show');
    Route::delete('/dashboard/registrations/{registration}/cancel', [EventRegistrationController::class, 'cancel'])->name('dashboard.registrations.cancel');

    // Manual Attendance Check-in (Backup if QR not working)
    Route::get('/dashboard/events/{event}/attendance', [DashboardController::class, 'showAttendanceForm'])->name('dashboard.attendance.form');
    Route::post('/dashboard/events/{event}/attendance', [DashboardController::class, 'submitAttendance'])->name('dashboard.attendance.submit');

    // Jury Review Routes (Paper submission handled by friend's system)
    Route::prefix('jury')->name('jury.')->group(function () {
        Route::get('/papers', [App\Http\Controllers\Jury\PaperReviewController::class, 'index'])->name('papers.index');
        Route::get('/papers/{assignment}', [App\Http\Controllers\Jury\PaperReviewController::class, 'show'])->name('papers.show');
        Route::get('/papers/{assignment}/download', [App\Http\Controllers\Jury\PaperReviewController::class, 'download'])->name('papers.download');
        Route::get('/papers/{assignment}/review', [App\Http\Controllers\Jury\PaperReviewController::class, 'createReview'])->name('papers.review');
        Route::post('/papers/{assignment}/review', [App\Http\Controllers\Jury\PaperReviewController::class, 'storeReview'])->name('papers.review.store');
        Route::post('/papers/{assignment}/accept', [App\Http\Controllers\Jury\PaperReviewController::class, 'acceptAssignment'])->name('papers.accept');
        Route::post('/papers/{assignment}/decline', [App\Http\Controllers\Jury\PaperReviewController::class, 'declineAssignment'])->name('papers.decline');
    });
});

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest routes
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AdminAuthController::class, 'login']);
    });

    // Protected routes
    Route::middleware('auth:admin')->group(function () {
        Route::get('dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');
        
        // Event Organizer Management
        Route::resource('organizers', EventOrganizerController::class);
        Route::post('organizers/{organizer}/approve', [EventOrganizerController::class, 'approve'])->name('organizers.approve');
        Route::post('organizers/{organizer}/reject', [EventOrganizerController::class, 'reject'])->name('organizers.reject');
        
        // Event Categories
        Route::resource('categories', EventCategoryController::class);
        
        // Reports
        Route::get('reports', [App\Http\Controllers\Admin\ReportsController::class, 'index'])->name('reports.index');
        Route::get('reports/export/organizers', [App\Http\Controllers\Admin\ReportsController::class, 'exportOrganizers'])->name('reports.export.organizers');
        
        // Advanced Features - Event Materials Management
        Route::get('materials', [App\Http\Controllers\Admin\MaterialController::class, 'index'])->name('materials.index');
        Route::get('materials/{material}', [App\Http\Controllers\Admin\MaterialController::class, 'show'])->name('materials.show');
        Route::get('materials/{material}/download', [App\Http\Controllers\Admin\MaterialController::class, 'download'])->name('materials.download');
        Route::delete('materials/{material}', [App\Http\Controllers\Admin\MaterialController::class, 'destroy'])->name('materials.destroy');
        
        // Advanced Features - Attendance Management
        Route::get('attendance', [App\Http\Controllers\Admin\AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('attendance/event/{event}', [App\Http\Controllers\Admin\AttendanceController::class, 'event'])->name('attendance.event');
        Route::get('attendance/export', [App\Http\Controllers\Admin\AttendanceController::class, 'export'])->name('attendance.export');
        
        // Advanced Features - QR Code Management
        Route::get('qr-codes', [App\Http\Controllers\Admin\QrCodeController::class, 'index'])->name('qr-codes.index');
        Route::get('qr-codes/{qrCode}', [App\Http\Controllers\Admin\QrCodeController::class, 'show'])->name('qr-codes.show');
        Route::delete('qr-codes/{qrCode}', [App\Http\Controllers\Admin\QrCodeController::class, 'destroy'])->name('qr-codes.destroy');
        
        // Advanced Features - Certificate Management
        Route::get('certificates', [App\Http\Controllers\Admin\CertificateController::class, 'index'])->name('certificates.index');
        Route::get('certificates/{certificate}', [App\Http\Controllers\Admin\CertificateController::class, 'show'])->name('certificates.show');
        Route::get('certificates/{certificate}/download', [App\Http\Controllers\Admin\CertificateController::class, 'download'])->name('certificates.download');
        Route::delete('certificates/{certificate}', [App\Http\Controllers\Admin\CertificateController::class, 'destroy'])->name('certificates.destroy');
        
        // QR Scanner for Admin
        Route::get('qr-scanner', [App\Http\Controllers\Admin\QrScannerController::class, 'index'])->name('qr.scanner');
        Route::post('qr-scan', [App\Http\Controllers\Admin\QrScannerController::class, 'scan'])->name('qr.scan');
    });
});

// Event Organizer Routes
Route::prefix('organizer')->name('organizer.')->group(function () {
    // Guest routes
    Route::middleware('guest:organizer')->group(function () {
        Route::get('register', [OrganizerAuthController::class, 'showRegistrationForm'])->name('register');
        Route::post('register', [OrganizerAuthController::class, 'register']);
        Route::get('login', [OrganizerAuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [OrganizerAuthController::class, 'login']);
    });

    // Protected routes
    Route::middleware('auth:organizer')->group(function () {
        Route::get('dashboard', [OrganizerDashboardController::class, 'index'])->name('dashboard');
        Route::post('logout', [OrganizerAuthController::class, 'logout'])->name('logout');
        
        // Profile & Settings
        Route::get('change-password', [OrganizerAuthController::class, 'showChangePasswordForm'])->name('change-password');
        Route::post('change-password', [OrganizerAuthController::class, 'changePassword'])->name('change-password.update');
        
        // Event Management
        Route::resource('events', EventController::class);
        Route::post('events/{event}/duplicate', [EventController::class, 'duplicate'])->name('events.duplicate');
        
        // Registration Management
        Route::get('registrations', [RegistrationController::class, 'index'])->name('registrations.index');
        Route::get('registrations/export', [RegistrationController::class, 'export'])->name('registrations.export');
        Route::get('registrations/event/{event}', [RegistrationController::class, 'event'])->name('registrations.event');
        Route::get('registrations/{registration}', [RegistrationController::class, 'show'])->name('registrations.show');
        Route::post('registrations/{registration}/status', [RegistrationController::class, 'updateStatus'])->name('registrations.update-status');
        Route::post('registrations/{registration}/payment', [RegistrationController::class, 'updatePaymentStatus'])->name('registrations.update-payment');
        Route::post('registrations/bulk-update', [RegistrationController::class, 'bulkUpdate'])->name('registrations.bulk-update');
        Route::post('registrations/{registration}/check-in', [RegistrationController::class, 'checkIn'])->name('registrations.check-in');
        Route::post('registrations/send-message', [RegistrationController::class, 'sendMessage'])->name('registrations.send-message');
        
        // Registration Approval Management
        Route::prefix('approvals')->name('approvals.')->group(function () {
            Route::get('/', [OrganizerApprovalController::class, 'index'])->name('index');
            Route::get('/events/{event}/registrations', [OrganizerApprovalController::class, 'eventRegistrations'])->name('event-registrations');
            Route::get('/registrations/{registration}', [OrganizerApprovalController::class, 'show'])->name('show');
            Route::post('/registrations/{registration}/approve', [OrganizerApprovalController::class, 'approve'])->name('approve');
            Route::post('/registrations/{registration}/reject', [OrganizerApprovalController::class, 'reject'])->name('reject');
            Route::post('/bulk-approve', [OrganizerApprovalController::class, 'bulkApprove'])->name('bulk-approve');
            Route::post('/bulk-reject', [OrganizerApprovalController::class, 'bulkReject'])->name('bulk-reject');
        });
        
        // Analytics & Reporting
        Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('analytics/export', [AnalyticsController::class, 'export'])->name('analytics.export');
        
        // Event Materials Management
        Route::resource('materials', App\Http\Controllers\Organizer\MaterialController::class);
        Route::get('materials/{material}/download', [App\Http\Controllers\Organizer\MaterialController::class, 'download'])->name('materials.download');
        Route::get('materials/{material}/analytics', [App\Http\Controllers\Organizer\MaterialController::class, 'analytics'])->name('materials.analytics');
        
        // Attendance Management
        Route::get('attendance', [App\Http\Controllers\Organizer\AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('attendance/scanner', [App\Http\Controllers\Organizer\AttendanceController::class, 'scanner'])->name('attendance.scanner');
        Route::post('attendance/qr-checkin', [App\Http\Controllers\Organizer\AttendanceController::class, 'qrCheckIn'])->name('attendance.qr-checkin');
        Route::get('attendance/event/{event}', [App\Http\Controllers\Organizer\AttendanceController::class, 'event'])->name('attendance.event');
        Route::post('attendance/event/{event}/manual-checkin', [App\Http\Controllers\Organizer\AttendanceController::class, 'manualCheckIn'])->name('attendance.manual-checkin');
        Route::post('attendance/event/{event}/manual-registration-checkin', [App\Http\Controllers\Organizer\AttendanceController::class, 'manualRegistrationCheckIn'])->name('attendance.manual-registration-checkin');
        Route::post('attendance/event/{event}/bulk-registration-checkin', [App\Http\Controllers\Organizer\AttendanceController::class, 'bulkRegistrationCheckIn'])->name('attendance.bulk-registration-checkin');
        Route::post('attendance/event/{event}/attendance/{attendance}/checkout', [App\Http\Controllers\Organizer\AttendanceController::class, 'manualCheckOut'])->name('attendance.manual-checkout');
        Route::post('attendance/event/{event}/bulk-checkin', [App\Http\Controllers\Organizer\AttendanceController::class, 'bulkCheckIn'])->name('attendance.bulk-checkin');
        Route::get('attendance/event/{event}/qr-scanner', [App\Http\Controllers\Organizer\AttendanceController::class, 'qrScanner'])->name('attendance.qr-scanner');
        Route::post('attendance/event/{event}/qr-scan', [App\Http\Controllers\Organizer\AttendanceController::class, 'processQrScan'])->name('attendance.process-qr');
        Route::get('attendance/event/{event}/export', [App\Http\Controllers\Organizer\AttendanceController::class, 'export'])->name('attendance.export');
        Route::get('attendance/event/{event}/analytics', [App\Http\Controllers\Organizer\AttendanceController::class, 'analytics'])->name('attendance.analytics');
        
        // QR Code Management (General)
        Route::get('qr-codes', [App\Http\Controllers\Organizer\QrCodeController::class, 'indexGeneral'])->name('qr-codes.index');
        Route::get('qr-codes/{qrCode}', [App\Http\Controllers\Organizer\QrCodeController::class, 'showGeneral'])->name('qr-codes.show');
        Route::get('qr-codes/{qrCode}/download', [App\Http\Controllers\Organizer\QrCodeController::class, 'downloadGeneral'])->name('qr-codes.download');
        Route::delete('qr-codes/{qrCode}', [App\Http\Controllers\Organizer\QrCodeController::class, 'destroyGeneral'])->name('qr-codes.destroy');
        
        // QR Code Management (Event-specific)
        Route::get('events/{event}/qr-codes', [App\Http\Controllers\Organizer\QrCodeController::class, 'index'])->name('events.qr-codes.index');
        Route::get('events/{event}/qr-codes/create', [App\Http\Controllers\Organizer\QrCodeController::class, 'create'])->name('events.qr-codes.create');
        Route::post('events/{event}/qr-codes', [App\Http\Controllers\Organizer\QrCodeController::class, 'store'])->name('events.qr-codes.store');
        Route::get('events/{event}/qr-codes/{qrCode}', [App\Http\Controllers\Organizer\QrCodeController::class, 'show'])->name('events.qr-codes.show');
        Route::get('events/{event}/qr-codes/{qrCode}/edit', [App\Http\Controllers\Organizer\QrCodeController::class, 'edit'])->name('events.qr-codes.edit');
        Route::put('events/{event}/qr-codes/{qrCode}', [App\Http\Controllers\Organizer\QrCodeController::class, 'update'])->name('events.qr-codes.update');
        Route::delete('events/{event}/qr-codes/{qrCode}', [App\Http\Controllers\Organizer\QrCodeController::class, 'destroy'])->name('events.qr-codes.destroy');
        Route::get('events/{event}/qr-codes/{qrCode}/download', [App\Http\Controllers\Organizer\QrCodeController::class, 'download'])->name('events.qr-codes.download');
        Route::post('events/{event}/qr-codes/{qrCode}/regenerate', [App\Http\Controllers\Organizer\QrCodeController::class, 'regenerate'])->name('events.qr-codes.regenerate');
        Route::post('events/{event}/qr-codes/bulk-generate', [App\Http\Controllers\Organizer\QrCodeController::class, 'bulkGenerate'])->name('events.qr-codes.bulk-generate');
        Route::get('events/{event}/qr-codes/{qrCode}/analytics', [App\Http\Controllers\Organizer\QrCodeController::class, 'analytics'])->name('events.qr-codes.analytics');
        
        // Certificate Management
        Route::get('certificates', [App\Http\Controllers\Organizer\CertificateController::class, 'index'])->name('certificates.index');
        Route::get('certificates/event/{event}', [App\Http\Controllers\Organizer\CertificateController::class, 'event'])->name('certificates.event');
        Route::post('certificates/bulk-email', [App\Http\Controllers\Organizer\CertificateController::class, 'bulkEmailGeneral'])->name('certificates.bulk-email-general');
        Route::get('certificates/bulk-download', [App\Http\Controllers\Organizer\CertificateController::class, 'bulkDownloadGeneral'])->name('certificates.bulk-download-general');
        Route::get('certificates/{certificate}', [App\Http\Controllers\Organizer\CertificateController::class, 'show'])->name('certificates.show');
        Route::post('certificates/event/{event}/generate', [App\Http\Controllers\Organizer\CertificateController::class, 'generate'])->name('certificates.generate');
        Route::post('certificates/event/{event}/bulk-generate', [App\Http\Controllers\Organizer\CertificateController::class, 'bulkGenerate'])->name('certificates.bulk-generate');
        Route::get('certificates/{certificate}/download', [App\Http\Controllers\Organizer\CertificateController::class, 'download'])->name('certificates.download');
        Route::post('certificates/{certificate}/email', [App\Http\Controllers\Organizer\CertificateController::class, 'email'])->name('certificates.email');
        Route::post('certificates/event/{event}/bulk-email', [App\Http\Controllers\Organizer\CertificateController::class, 'bulkEmail'])->name('certificates.bulk-email');
        
        // Attendance-based Certificate Generation
        Route::get('certificates/event/{event}/attendance-summary', [App\Http\Controllers\Organizer\CertificateController::class, 'attendanceSummary'])->name('certificates.attendance-summary');
        Route::get('certificates/event/{event}/eligible-attendees', [App\Http\Controllers\Organizer\CertificateController::class, 'eligibleAttendees'])->name('certificates.eligible-attendees');
        Route::post('certificates/event/{event}/generate-from-attendance', [App\Http\Controllers\Organizer\CertificateController::class, 'generateFromAttendance'])->name('certificates.generate-from-attendance');
        
        // Paper Management Routes
        Route::prefix('events/{event}/papers')->name('events.papers.')->group(function () {
            Route::get('/', [App\Http\Controllers\Organizer\PaperManagementController::class, 'index'])->name('index');
            Route::get('/{paper}', [App\Http\Controllers\Organizer\PaperManagementController::class, 'show'])->name('show');
            Route::get('/{paper}/download', [App\Http\Controllers\Organizer\PaperManagementController::class, 'download'])->name('download');
            Route::post('/{paper}/assign-jury', [App\Http\Controllers\Organizer\PaperManagementController::class, 'assignJury'])->name('assign-jury');
            Route::delete('/{paper}/jury/{assignment}', [App\Http\Controllers\Organizer\PaperManagementController::class, 'removeJury'])->name('remove-jury');
            Route::post('/{paper}/update-status', [App\Http\Controllers\Organizer\PaperManagementController::class, 'updateStatus'])->name('update-status');
        });
        
        // Jury Assignment Routes (for Innovation Competitions)
        Route::prefix('events/{event}/jury-assignments')->name('events.jury-assignments.')->group(function () {
            Route::get('/', [App\Http\Controllers\Organizer\JuryAssignmentController::class, 'index'])->name('index');
            Route::post('/participants/{participant}/assign', [App\Http\Controllers\Organizer\JuryAssignmentController::class, 'assign'])->name('assign');
            Route::post('/participants/{participant}/assign-multiple', [App\Http\Controllers\Organizer\JuryAssignmentController::class, 'assignMultiple'])->name('assign-multiple');
            Route::delete('/{assignment}', [App\Http\Controllers\Organizer\JuryAssignmentController::class, 'remove'])->name('remove');
            Route::post('/auto-assign', [App\Http\Controllers\Organizer\JuryAssignmentController::class, 'autoAssign'])->name('auto-assign');
            Route::post('/clear-all', [App\Http\Controllers\Organizer\JuryAssignmentController::class, 'clearAll'])->name('clear-all');
        });
        
        // Jury Mapping Overview
        Route::get('jury-mapping', [App\Http\Controllers\Organizer\JuryMappingController::class, 'index'])->name('jury-mapping.index');
        Route::get('jury-mapping/{event}', [App\Http\Controllers\Organizer\JuryMappingController::class, 'show'])->name('jury-mapping.show');
    });
});
