<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\City\CityController;
use App\Http\Controllers\Api\Country\CountryController;
use App\Http\Controllers\Api\Order\OrderController;
use App\Http\Controllers\Api\Order\OrderQuickActionController;
use App\Http\Controllers\Api\Priority\PriorityController;
use App\Http\Controllers\Api\Province\ProvinceController;
use App\Http\Controllers\Api\Role\RoleController;
use App\Http\Controllers\Api\Status\StatusController;
use App\Http\Controllers\Api\User\UserController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::prefix('orders')->group(function() {
        Route::get('/', [OrderController::class, 'index']);
        Route::get('/{id}', [OrderController::class, 'show']);
        Route::post('/', [OrderController::class, 'store']);
        Route::put('/', [OrderController::class, 'update']);
        Route::delete('/{id}', [OrderController::class, 'delete']);
        Route::post('/mark-as-completed', [OrderQuickActionController::class, 'markAsCompleted']);
    });

    Route::prefix('priorities')->group(function() {
        Route::get('/', [PriorityController::class, 'index']);
    });

    Route::prefix('statuses')->group(function() {
        Route::get('/', [StatusController::class, 'index']);
    });

    Route::prefix('countries')->group(function() {
        Route::get('/', [CountryController::class, 'index']);
    });

    Route::prefix('provinces')->group(function() {
        Route::get('/', [ProvinceController::class, 'index']);
    });

    Route::prefix('cities')->group(function() {
        Route::get('/', [CityController::class, 'index']);
    });

    Route::prefix('roles')->group(function() {
        Route::get('/', [RoleController::class, 'index']);
    });

    Route::prefix('users')->group(function() {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::post('/', [UserController::class, 'store']);
        Route::put('/', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'delete']);
    });
});
