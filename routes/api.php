<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FinancialController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\TransactionApiController;
use App\Http\Controllers\Api\DashboardApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Authentication
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // User Profile
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/profile', [UserController::class, 'profile']);
    Route::put('/profile', [UserController::class, 'updateProfile']);
    Route::put('/change-password', [UserController::class, 'changePassword']);
    
    // Balance/Wallet Management
    Route::get('/balances', [FinancialController::class, 'balances']);
    Route::post('/balances', [FinancialController::class, 'createBalance']);
    Route::get('/balances/{id}', [FinancialController::class, 'getBalance']);
    Route::put('/balances/{id}', [FinancialController::class, 'updateBalance']);    
    Route::delete('/balances/{id}', [FinancialController::class, 'deleteBalance']);
    Route::get('/total-balance', [FinancialController::class, 'totalBalance']);
    
    // Category Management
    Route::get('/categories', [CategoryApiController::class, 'index']);
    Route::post('/categories', [CategoryApiController::class, 'store']);
    Route::get('/categories/{id}', [CategoryApiController::class, 'show']);
    Route::put('/categories/{id}', [CategoryApiController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryApiController::class, 'destroy']);
    Route::get('/categories/income', [CategoryApiController::class, 'getIncomeCategories']);
    Route::get('/categories/expense', [CategoryApiController::class, 'getExpenseCategories']);
    
    // Transaction Management
    Route::get('/transactions', [TransactionApiController::class, 'index']);
    Route::post('/transactions', [TransactionApiController::class, 'store']);
    Route::get('/transactions/recent', [TransactionApiController::class, 'recent']);
    Route::get('/transactions/{id}', [TransactionApiController::class, 'show']);
    Route::put('/transactions/{id}', [TransactionApiController::class, 'update']);
    Route::delete('/transactions/{id}', [TransactionApiController::class, 'destroy']);
    
    // Dashboard
    Route::get('/dashboard', [DashboardApiController::class, 'index']);
    Route::get('/dashboard/summary', [DashboardApiController::class, 'summary']);
    Route::get('/dashboard/charts', [DashboardApiController::class, 'charts']);
});
