<?php

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
