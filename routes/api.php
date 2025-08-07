<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FinancialController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [UserController::class, 'profile']);
    Route::get('/balances', [FinancialController::class, 'balances']);
    Route::get('/total-balance', [FinancialController::class, 'totalBalance']);
});
