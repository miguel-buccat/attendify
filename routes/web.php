<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SiteSettingsController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\SiteAssetController;
use App\Http\Controllers\Student\AttendanceCalendarController;
use App\Http\Controllers\Student\AttendanceScanController;
use App\Http\Controllers\Student\ClassEnrollmentController;
use App\Http\Controllers\Student\ExcuseRequestController as StudentExcuseRequestController;
use App\Http\Controllers\Student\NotificationPreferenceController;
use App\Http\Controllers\Teacher\AttendanceController;
use App\Http\Controllers\Teacher\ClassAnalyticsController;
use App\Http\Controllers\Teacher\ClassController;
use App\Http\Controllers\Teacher\ClassSessionController;
use App\Http\Controllers\Teacher\ExcuseRequestController as TeacherExcuseRequestController;
use App\Http\Controllers\Teacher\StudentPerformanceController;
use Illuminate\Support\Facades\Route;

// Route::get('/', [LandingController::class, 'index'])->name('landing');

Route::get('/new', [SetupController::class, 'newIndex'])->name('new.index');
Route::get('/new/setup', [SetupController::class, 'index'])->name('new.setup');
Route::post('/new/setup/admin', [SetupController::class, 'storeAdmin'])->name('new.setup.admin');
Route::post('/new/setup/settings', [SetupController::class, 'storeSettings'])->name('new.setup.settings');

Route::get('/site-assets/{key}', [SiteAssetController::class, 'show'])
    ->whereIn('key', ['institution_logo', 'landing_banner'])
    ->name('site-assets.show');

Route::middleware('guest')->group(function (): void {
    Route::get('/', [LandingController::class, 'index'])->name('landing');

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store')->middleware('throttle:5,1');

    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email')->middleware('throttle:3,1');

    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update')->middleware('throttle:3,1');

    Route::get('/invitation/accept/{token}', [InvitationController::class, 'show'])->name('invitation.accept');
    Route::post('/invitation/accept/{token}', [InvitationController::class, 'store'])->name('invitation.accept.store');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/dashboard', fn () => redirect()->route('dashboard'))->name('dashboard');
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/invite', [UserManagementController::class, 'invite'])->name('users.invite');
    Route::post('/users/invite', [UserManagementController::class, 'sendInvitation'])->name('users.invite.send');
    Route::get('/users/{user}', [UserManagementController::class, 'show'])->name('users.show');
    Route::patch('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::post('/users/{user}/block', [UserManagementController::class, 'block'])->name('users.block');
    Route::post('/users/{user}/unblock', [UserManagementController::class, 'unblock'])->name('users.unblock');
    Route::post('/users/{user}/archive', [UserManagementController::class, 'archive'])->name('users.archive');
    Route::delete('/invitations/{invitation}', [UserManagementController::class, 'invalidateInvitation'])->name('invitations.invalidate');
    Route::get('/settings', [SiteSettingsController::class, 'edit'])->name('settings.edit');
    Route::patch('/settings', [SiteSettingsController::class, 'update'])->name('settings.update');
    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/csv', [ReportController::class, 'exportCsv'])->name('reports.export.csv');
    Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
    Route::get('/leaderboard', [ReportController::class, 'leaderboard'])->name('leaderboard.index');
});

Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function (): void {
    Route::get('/dashboard', fn () => redirect()->route('dashboard'))->name('dashboard');
    Route::get('/classes', [ClassController::class, 'index'])->name('classes.index');
    Route::get('/classes/create', [ClassController::class, 'create'])->name('classes.create');
    Route::post('/classes', [ClassController::class, 'store'])->name('classes.store');
    Route::get('/classes/{class}', [ClassController::class, 'show'])->name('classes.show');
    Route::patch('/classes/{class}', [ClassController::class, 'update'])->name('classes.update');
    Route::post('/classes/{class}/archive', [ClassController::class, 'archive'])->name('classes.archive');
    Route::get('/classes/{class}/students/search', [ClassController::class, 'searchStudents'])->name('classes.students.search');
    Route::post('/classes/{class}/enroll', [ClassController::class, 'enroll'])->name('classes.enroll');
    Route::delete('/classes/{class}/students/{student}', [ClassController::class, 'unenroll'])->name('classes.unenroll');
    Route::post('/classes/{class}/sessions', [ClassSessionController::class, 'store'])->name('sessions.store');
    Route::get('/sessions/{session}', [ClassSessionController::class, 'show'])->name('sessions.show');
    Route::post('/sessions/{session}/start', [ClassSessionController::class, 'start'])->name('sessions.start');
    Route::post('/sessions/{session}/complete', [ClassSessionController::class, 'complete'])->name('sessions.complete');
    Route::post('/sessions/{session}/cancel', [ClassSessionController::class, 'cancel'])->name('sessions.cancel');
    Route::post('/sessions/{session}/cancel-upcoming', [ClassSessionController::class, 'cancelUpcoming'])->name('sessions.cancel-upcoming');
    Route::get('/sessions/{session}/attendance', [ClassSessionController::class, 'attendanceData'])->name('sessions.attendance');
    Route::get('/sessions/{session}/attendance/manage', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::patch('/attendance/{record}', [AttendanceController::class, 'update'])->name('attendance.update');
    Route::get('/sessions/{session}/attendance/export', [AttendanceController::class, 'export'])->name('attendance.export');
    Route::get('/sessions/{session}/attendance/export-pdf', [AttendanceController::class, 'exportPdf'])->name('attendance.export-pdf');
    Route::get('/classes/{class}/students/{student}', [StudentPerformanceController::class, 'show'])->name('students.show');
    Route::get('/classes/{class}/analytics/pdf', [ClassAnalyticsController::class, 'exportPdf'])->name('classes.analytics.pdf');
    Route::get('/excuses', [TeacherExcuseRequestController::class, 'index'])->name('excuses.index');
    Route::patch('/excuses/{excuseRequest}', [TeacherExcuseRequestController::class, 'review'])->name('excuses.review');
    Route::get('/excuses/{excuseRequest}/download', [TeacherExcuseRequestController::class, 'download'])->name('excuses.download');
});

Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function (): void {
    Route::get('/dashboard', fn () => redirect()->route('dashboard'))->name('dashboard');
    Route::get('/classes', [ClassEnrollmentController::class, 'index'])->name('classes.index');
    Route::get('/classes/{class}', [ClassEnrollmentController::class, 'show'])->name('classes.show');
    Route::get('/scan', [AttendanceScanController::class, 'index'])->name('scan.index');
    Route::post('/scan', [AttendanceScanController::class, 'store'])->name('scan.store');
    Route::get('/attendance', [AttendanceScanController::class, 'history'])->name('attendance.index');
    Route::get('/calendar', [AttendanceCalendarController::class, 'index'])->name('calendar.index');
    Route::get('/notifications', [NotificationPreferenceController::class, 'edit'])->name('notifications.edit');
    Route::patch('/notifications', [NotificationPreferenceController::class, 'update'])->name('notifications.update');
    Route::get('/excuses', [StudentExcuseRequestController::class, 'index'])->name('excuses.index');
    Route::get('/excuses/create', [StudentExcuseRequestController::class, 'create'])->name('excuses.create');
    Route::post('/excuses', [StudentExcuseRequestController::class, 'store'])->name('excuses.store');
    Route::get('/excuses/{excuseRequest}/download', [StudentExcuseRequestController::class, 'download'])->name('excuses.download');
});
