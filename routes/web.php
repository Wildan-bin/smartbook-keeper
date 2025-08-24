<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BalanceController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;

Route::get('/', function () {
return Inertia::render('landing/welcome');
})->name('home');

Route::get('/beranda', function () {
    return Inertia::render('Beranda');
})->name('beranda');



Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Transaction & Balance routes
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::put('/transactions/{transaction}', [TransactionController::class, 'update'])->name('transactions.update');
    Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');
    
    Route::get('/balances', [TransactionController::class, 'balances'])->name('balances.index');
    Route::post('/balances', [TransactionController::class, 'storeBalance'])->name('balances.store');
    Route::post('/balances/category', [TransactionController::class, 'storeCategory'])->name('balances.category.store');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/pdf-preview', [ReportController::class, 'pdfPreview'])->name('reports.pdf-preview');
    Route::get('/reports/download-pdf', [ReportController::class, 'downloadPdf'])->name('reports.download-pdf');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
