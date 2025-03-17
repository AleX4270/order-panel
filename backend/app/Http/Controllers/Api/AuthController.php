<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\HttpStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\Api\Auth\AuthService;
use Illuminate\Http\Request;
use App\Http\Responses\Api\ApiResponse;

class AuthController extends Controller {
    public function __construct(
        private readonly AuthService $authService,
    ) {}

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
            message: __('basic.success')
        );
    }

    public function register(RegisterRequest $request): ApiResponse {
        $this->authService->register($request->validated());

        return new ApiResponse(
            status: HttpStatus::OK,
            message: __('basic.success')
        );
    }

    public function logout(Request $request): ApiResponse {
        $this->authService->logout($request);

        return new ApiResponse(
            status: HttpStatus::OK,
            message: __('basic.success')
        );
    }
}
