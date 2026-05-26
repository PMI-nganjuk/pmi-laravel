<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChartOfAccountController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\SettingController;

Route::resource('coa', ChartOfAccountController::class)->except(['show', 'edit']);
Route::resource('programs', ProgramController::class)->except(['show', 'edit'])->middleware('auth');
Route::resource('programs', ProgramController::class)->except(['show', 'edit'])->middleware('auth');

Route::prefix('coa')
    ->name('coa.')
    ->group(function () {

        Route::get(
            '/category-two',
            [ChartOfAccountController::class, 'getCategoryTwo']
        )->name('category-two');

        Route::get(
            '/generate-code',
            [ChartOfAccountController::class, 'generateCode']
        )->name('generate-code');
    });

// Redirect root to welcome
// Redirect root to welcome
Route::redirect('/', '/welcome');

Route::get('/welcome', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('welcome');


Route::get('/welcome', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('welcome');


// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware(['guest', 'throttle:login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Dashboard route protected by auth middleware
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

// Admin-only user management routes
Route::middleware(['auth', 'can:manage-users'])->prefix('dashboard/users')->name('users.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::put('/{user}', [UserController::class, 'update'])->name('update');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
});

// Admin-only system configuration (settings) routes
Route::middleware(['auth', 'can:manage-settings'])->prefix('dashboard/settings')->name('settings.')->group(function () {
    Route::get('/', [SettingController::class, 'index'])->name('index');
    Route::put('/', [SettingController::class, 'update'])->name('update');
});

// Profile routes — accessible by all authenticated users
Route::middleware('auth')->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'show'])->name('show');
    Route::put('/info', [ProfileController::class, 'updateInfo'])->name('update-info');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('update-password');
});
