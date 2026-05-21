<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\City\CityController;
use App\Http\Controllers\Api\Company\CompanyController;
use App\Http\Controllers\Api\Country\CountryController;
use App\Http\Controllers\Api\Notification\NotificationController;
use App\Http\Controllers\Api\NotificationChannel\NotificationChannelController;
use App\Http\Controllers\Api\NotificationEvent\NotificationEventController;
use App\Http\Controllers\Api\Order\OrderController;
use App\Http\Controllers\Api\Order\OrderQuickActionController;
use App\Http\Controllers\Api\Priority\PriorityController;
use App\Http\Controllers\Api\Province\ProvinceController;
use App\Http\Controllers\Api\OrderRequest\OrderRequestController;
use App\Http\Controllers\Api\Role\RoleController;
use App\Http\Controllers\Api\Status\StatusController;
use App\Http\Controllers\Api\User\UserController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware(['throttle:public'])->group(function() {
    Route::post('/order-request', [OrderRequestController::class, 'store']);

    Route::prefix('countries')->group(function() {
        Route::get('/', [CountryController::class, 'index']);
    });

    Route::prefix('provinces')->group(function() {
        Route::get('/', [ProvinceController::class, 'index']);
    });
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::prefix('order-requests')->group(function() {
        Route::get('/', [OrderRequestController::class, 'index']);
    });

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

    Route::prefix('notification-channels')->group(function() {
        Route::get('/', [NotificationChannelController::class, 'index']);
    });

    Route::prefix('notification-events')->group(function() {
        Route::get('/', [NotificationEventController::class, 'index']);
    });

    Route::prefix('notifications')->group(function() {
        Route::get('/', [NotificationController::class, 'index']);
        Route::post('/mark-as-read', [NotificationController::class, 'markAsRead']);
    });

    Route::prefix('company')->group(function() {
        Route::get('/', [CompanyController::class, 'show']);
        Route::put('/', [CompanyController::class, 'update']);
    });
});
