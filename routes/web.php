<?php

use App\Http\Controllers\Api\WaterQualityDataController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

// Preview/experimental routes for testing new designs
Route::view('/preview', 'landing')->name('preview');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('account_management', 'account_management')
    ->middleware(['auth', 'verified', 'isAdmin'])
    ->name('account_management');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::view('optimal-fish', 'optimal-fish')
    ->middleware(['auth'])
    ->name('optimal-fish');

Route::view('historical-data', 'historical-data')
    ->middleware(['auth'])
    ->name('historical-data');

Route::post('store-water-quality-data', [WaterQualityDataController::class, 'store']);

require __DIR__.'/auth.php';
