<?php

use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseRecipientController;
use App\Http\Controllers\MandatoryExpenseController;
use App\Http\Controllers\OperationalExpenseController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\Revenue\VASRevenueController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
});

Route::middleware(['auth'])->prefix('admin/settings')->name('admin.settings.')->group(function () {
    Route::get('/', [SettingsController::class, 'index'])->name('index');
    Route::get('/services', [SettingsController::class, 'section'])->defaults('section', 'services')->name('sections.services');
    Route::get('/service-types', [SettingsController::class, 'section'])->defaults('section', 'service-types')->name('sections.service-types');
    Route::get('/mnos', [SettingsController::class, 'section'])->defaults('section', 'mnos')->name('sections.mnos');
    Route::get('/aggregators', [SettingsController::class, 'section'])->defaults('section', 'aggregators')->name('sections.aggregators');
    Route::get('/banks', [SettingsController::class, 'section'])->defaults('section', 'banks')->name('sections.banks');
    Route::get('/mandatory-types', [SettingsController::class, 'section'])->defaults('section', 'mandatory-types')->name('sections.mandatory-types');
    Route::get('/operational-categories', [SettingsController::class, 'section'])->defaults('section', 'operational-categories')->name('sections.operational-categories');
    Route::get('/recipients', [SettingsController::class, 'section'])->defaults('section', 'recipients')->name('sections.recipients');

    Route::post('/services', [SettingsController::class, 'storeService'])->name('services.store');
    Route::patch('/services/{service}', [SettingsController::class, 'updateService'])->name('services.update');
    Route::delete('/services/{service}', [SettingsController::class, 'destroyService'])->name('services.destroy');
    Route::post('/services/{service}/shares', [SettingsController::class, 'storeServiceShare'])->name('services.shares.store');
    Route::patch('/services/shares/{servicePartnerShare}', [SettingsController::class, 'updateServiceShare'])->name('services.shares.update');

    Route::post('/service-types', [SettingsController::class, 'storeServiceType'])->name('service-types.store');
    Route::patch('/service-types/{serviceType}', [SettingsController::class, 'updateServiceType'])->name('service-types.update');
    Route::delete('/service-types/{serviceType}', [SettingsController::class, 'destroyServiceType'])->name('service-types.destroy');

    Route::post('/mnos', [SettingsController::class, 'storeMno'])->name('mnos.store');
    Route::patch('/mnos/{mno}', [SettingsController::class, 'updateMno'])->name('mnos.update');
    Route::delete('/mnos/{mno}', [SettingsController::class, 'destroyMno'])->name('mnos.destroy');

    Route::post('/aggregators', [SettingsController::class, 'storeAggregator'])->name('aggregators.store');
    Route::patch('/aggregators/{aggregator}', [SettingsController::class, 'updateAggregator'])->name('aggregators.update');
    Route::delete('/aggregators/{aggregator}', [SettingsController::class, 'destroyAggregator'])->name('aggregators.destroy');

    Route::post('/banks', [SettingsController::class, 'storeBank'])->name('banks.store');
    Route::patch('/banks/{bank}', [SettingsController::class, 'updateBank'])->name('banks.update');
    Route::delete('/banks/{bank}', [SettingsController::class, 'destroyBank'])->name('banks.destroy');

    Route::post('/mandatory-types', [SettingsController::class, 'storeMandatoryType'])->name('mandatory-types.store');
    Route::patch('/mandatory-types/{mandatoryExpenseType}', [SettingsController::class, 'updateMandatoryType'])->name('mandatory-types.update');
    Route::delete('/mandatory-types/{mandatoryExpenseType}', [SettingsController::class, 'destroyMandatoryType'])->name('mandatory-types.destroy');

    Route::post('/operational-categories', [SettingsController::class, 'storeOperationalCategory'])->name('operational-categories.store');
    Route::patch('/operational-categories/{operationalCategory}', [SettingsController::class, 'updateOperationalCategory'])->name('operational-categories.update');
    Route::delete('/operational-categories/{operationalCategory}', [SettingsController::class, 'destroyOperationalCategory'])->name('operational-categories.destroy');

    Route::post('/recipients', [SettingsController::class, 'storeRecipient'])->name('recipients.store');
    Route::patch('/recipients/{expenseRecipient}', [SettingsController::class, 'updateRecipient'])->name('recipients.update');
    Route::delete('/recipients/{expenseRecipient}', [SettingsController::class, 'destroyRecipient'])->name('recipients.destroy');
});

Route::middleware(['auth'])->prefix('reports')->name('reports.')->group(function () {
    Route::get('/revenue',     [ReportsController::class, 'revenue'])->name('revenue');
    Route::get('/services',    [ReportsController::class, 'services'])->name('services');
    Route::get('/mnos',        [ReportsController::class, 'mnos'])->name('mnos');
    Route::get('/aggregators', [ReportsController::class, 'aggregators'])->name('aggregators');
});

Route::middleware(['auth'])->prefix('payments')->name('payments.')->group(function () {
    Route::get('/', [PaymentsController::class, 'index'])->name('index');
    Route::get('/{vasRevenue}', [PaymentsController::class, 'show'])->name('show')->whereNumber('vasRevenue');
    Route::patch('/items/{paymentItem}', [PaymentsController::class, 'update'])->name('items.update');
    Route::get('/mandatory', [PaymentsController::class, 'mandatory'])->name('mandatory');
    Route::match(['patch', 'post'], '/mandatory/{accumulation}', [PaymentsController::class, 'updateMandatoryAccumulation'])->name('mandatory.update');
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
