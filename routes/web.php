<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CashDisbursementController;
use App\Http\Controllers\CashReceiptController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChartOfAccountController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\AdjustingEntryController;
use App\Http\Controllers\GeneralLedgerController;
use App\Http\Controllers\ProfitLossController;


// Finance: Cash Receipts (Penerimaan Kas)
Route::middleware(['auth', 'can:finance.view'])
    ->prefix('receipts')
    ->name('receipts.')
    ->group(function () {
        Route::get('/', [CashReceiptController::class, 'index'])->name('index');
        Route::post('/', [CashReceiptController::class, 'store'])
            ->middleware('can:finance.create')->name('store');
        Route::put('/{transaction}', [CashReceiptController::class, 'update'])
            ->middleware('can:finance.create')->name('update');
        Route::delete('/{transaction}', [CashReceiptController::class, 'destroy'])
            ->middleware('can:finance.create')->name('destroy');
        Route::get('/cash-accounts', [CashReceiptController::class, 'getCashAccounts'])->name('cash-accounts');
        Route::get('/transaction-accounts', [CashReceiptController::class, 'getTransactionAccounts'])->name('transaction-accounts');
        Route::get('/suggest-description', [CashReceiptController::class, 'suggestDescription'])->name('suggest-description');
    });


// Finance: Cash Disbursements (Pengeluaran Kas)
Route::middleware(['auth', 'can:finance.view'])
    ->prefix('disbursements')
    ->name('disbursements.')
    ->group(function () {
        Route::get('/', [CashDisbursementController::class, 'index'])->name('index');
        Route::post('/', [CashDisbursementController::class, 'store'])
            ->middleware('can:finance.create')->name('store');
        Route::put('/{transaction}', [CashDisbursementController::class, 'update'])
            ->middleware('can:finance.create')->name('update');
        Route::delete('/{transaction}', [CashDisbursementController::class, 'destroy'])
            ->middleware('can:finance.create')->name('destroy');
        Route::get('/cash-accounts', [CashDisbursementController::class, 'getCashAccounts'])->name('cash-accounts');
        Route::get('/transaction-accounts', [CashDisbursementController::class, 'getTransactionAccounts'])->name('transaction-accounts');
        Route::get('/suggest-description', [CashDisbursementController::class, 'suggestDescription'])->name('suggest-description');
    });


// Finance: Adjusting Entries (Jurnal Penyesuaian)
Route::middleware(['auth', 'can:finance.view'])
    ->prefix('adjusting-entries')
    ->name('adjusting-entries.')
    ->group(function () {
        Route::get('/', [AdjustingEntryController::class, 'index'])->name('index');
        Route::post('/', [AdjustingEntryController::class, 'store'])
            ->middleware('can:finance.create')->name('store');
        Route::put('/{transaction}', [AdjustingEntryController::class, 'update'])
            ->middleware('can:finance.create')->name('update');
        Route::delete('/{transaction}', [AdjustingEntryController::class, 'destroy'])
            ->middleware('can:finance.create')->name('destroy');
    });


// Finance: General Ledger Report (Buku Besar)
Route::middleware(['auth', 'can:finance.view'])
    ->get('/general-ledger', [GeneralLedgerController::class, 'index'])
    ->name('general-ledger.index');

// Laporan Laba Rugi
Route::middleware(['auth', 'can:finance.view'])
    ->get('/profit-loss', [ProfitLossController::class, 'index'])
    ->name('profit-loss.index');

Route::middleware(['auth', 'can:finance.view'])
    ->get('/profit-loss/export', [ProfitLossController::class, 'export'])
    ->name('profit-loss.export');


Route::resource('coa', ChartOfAccountController::class)->except(['show', 'edit']);
Route::resource('programs', ProgramController::class)->except(['show', 'edit'])->middleware('auth');

Route::prefix('coa')
    ->name('coa.')
    ->group(function () {

        Route::get(
            '/account-subcategory',
            [ChartOfAccountController::class, 'getAccountSubcategory']
        )->name('account-subcategory');

        Route::get(
            '/generate-code',
            [ChartOfAccountController::class, 'generateCode']
        )->name('generate-code');
    });

// Redirect root to welcome
Route::redirect('/', '/welcome');

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