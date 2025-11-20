<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseRecipientController;
use App\Http\Controllers\MandatoryExpenseController;
use App\Http\Controllers\OperationalExpenseController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\Revenue\VASRevenueController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
});

Route::middleware(['auth'])->prefix('reports')->name('reports.')->group(function () {
    Route::get('/revenue',     [ReportsController::class, 'revenue'])->name('revenue');
    Route::get('/services',    [ReportsController::class, 'services'])->name('services');
    Route::get('/mnos',        [ReportsController::class, 'mnos'])->name('mnos');
    Route::get('/aggregators', [ReportsController::class, 'aggregators'])->name('aggregators');
});

Route::middleware(['auth'])->prefix('revenue')->name('revenue.')->group(function () {
    Route::get('/',              [VASRevenueController::class, 'index'])->name('index');
    Route::get('/create',        [VASRevenueController::class, 'create'])->name('create');
    Route::post('/',             [VASRevenueController::class, 'store'])->name('store');
    Route::get('/{id}',          [VASRevenueController::class, 'show'])->name('show')->whereNumber('id');
    Route::post('/{id}/recompute',[VASRevenueController::class, 'recompute'])->name('recompute')->whereNumber('id');
    Route::post('/{id}/update',  [VASRevenueController::class, 'update'])->name('update')->whereNumber('id');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/revenue/{vasRevenue}/mandatory-expenses', [MandatoryExpenseController::class, 'store'])
        ->name('mandatory-expenses.store');
    Route::delete('/revenue/{vasRevenue}/mandatory-expenses/{mandatoryExpense}', [MandatoryExpenseController::class, 'destroy'])
        ->name('mandatory-expenses.destroy');

    Route::post('/revenue/{vasRevenue}/operational-expenses', [OperationalExpenseController::class, 'store'])
        ->name('operational-expenses.store');
    Route::delete('/revenue/{vasRevenue}/operational-expenses/{operationalExpense}', [OperationalExpenseController::class, 'destroy'])
        ->name('operational-expenses.destroy');

    // Expense Recipients
    Route::post('/expense-recipients', [ExpenseRecipientController::class, 'store'])
        ->name('expense-recipients.store');
    Route::get('/expense-recipients/by-category', [ExpenseRecipientController::class, 'byCategory'])
        ->name('expense-recipients.by-category');
});

require __DIR__.'/auth.php';
