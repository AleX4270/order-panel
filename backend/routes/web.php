<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Order\OrderController;
use App\Http\Controllers\Api\Priority\PriorityController;
use App\Http\Controllers\Api\Status\StatusController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::prefix('orders')->group(function() {
        Route::get('/', [OrderController::class, 'index']);
        Route::post('/', [OrderController::class, 'store']);
        Route::patch('/{id}', [OrderController::class, 'update']);
        Route::delete('/{id}', [OrderController::class, 'destroy']);
    });

    Route::prefix('priorities')->group(function() {
        Route::get('/', [PriorityController::class, 'index']);
    });

    Route::prefix('statuses')->group(function() {
        Route::get('/', [StatusController::class, 'index']);
    });
});
