<?php

declare(strict_types=1);

namespace App\Exceptions\Api\Auth;

use Illuminate\Http\Request;
use App\Http\Responses\Api\ApiResponse;
use App\Enums\HttpStatus;
use Illuminate\Contracts\Debug\ShouldntReport;
use RuntimeException;

class InvalidCredentialsException extends RuntimeException implements ShouldntReport {
    public function render(Request $request): ApiResponse {
        return new ApiResponse(
            status: HttpStatus::UNAUTHORIZED,
            message: __('auth.failed')
        );
    }
}
