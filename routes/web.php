<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChartOfAccountController;

Route::resource('coa', ChartOfAccountController::class)->except(['show', 'edit']);

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

// Redirect root to dashboard (which automatically handles auth/guest redirects)
Route::redirect('/', '/welcome');

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware(['guest', 'throttle:login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Dashboard route protected by auth middleware
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

// Admin-only user management routes
Route::middleware(['auth', 'can:manage-users'])->prefix('dashboard/users')->name('users.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/create', [UserController::class, 'create'])->name('create');
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
    Route::put('/{user}', [UserController::class, 'update'])->name('update');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
});
