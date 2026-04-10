<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\SiteAssetController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('landing');

Route::get('/new', [SetupController::class, 'newIndex'])->name('new.index');
Route::get('/new/setup', [SetupController::class, 'index'])->name('new.setup');
Route::post('/new/setup/admin', [SetupController::class, 'storeAdmin'])->name('new.setup.admin');
Route::post('/new/setup/settings', [SetupController::class, 'storeSettings'])->name('new.setup.settings');

Route::get('/site-assets/{key}', [SiteAssetController::class, 'show'])
    ->whereIn('key', ['institution_logo', 'landing_banner'])
    ->name('site-assets.show');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');

    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
