<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\HttpStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\Api\Auth\AuthService;
use Illuminate\Http\Request;
use App\Http\Responses\Api\ApiResponse;

class AuthController {
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    public function user(Request $request): ApiResponse {
        $currentUser = $request->user();

        return new ApiResponse(
            status: HttpStatus::OK,
            data: $currentUser,
            message: __('response.success'),
        );
    }

    public function login(LoginRequest $request): ApiResponse {
        $result = $this->authService->login($request, $request->validated());

        if(!$result) {
            return new ApiResponse(
                status: HttpStatus::UNAUTHORIZED,
                message: __('auth.failed')
            );
        }

        return new ApiResponse(
            status: HttpStatus::OK,
            data: $result,
            message: __('response.success')
        );
    }

    public function register(RegisterRequest $request): ApiResponse {
        $this->authService->register($request->validated());

        return new ApiResponse(
            status: HttpStatus::OK,
            message: __('response.success')
        );
    }

    public function logout(Request $request): ApiResponse {
        $this->authService->logout($request);

        return new ApiResponse(
            status: HttpStatus::OK,
            message: __('response.success')
        );
    }
}
