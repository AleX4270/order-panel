<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\HttpStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\Api\AuthService;
use Illuminate\Http\Request;
use App\Http\Responses\Api\ApiResponse;

class AuthController extends Controller {
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    public function login(LoginRequest $request): ApiResponse {
        $result = $this->authService->login($request->validated());

        if(!$result) {
            return new ApiResponse(
                statusCode: HttpStatus::UNAUTHORIZED,
                message: !empty($result['message']) ? $result['message'] : __('auth.failed')
            );
        }

        return new ApiResponse(
            statusCode: HttpStatus::OK,
            message: __('basic.success')
        );
    }

    // public function register(RegisterRequest $request): ApiResponse {
    //     $result = $this->authService->register($request->validated());
    // }

    // public function logout(Request $request): ApiResponse {
    //     $result = $this->authService->logout();
    // }
}
