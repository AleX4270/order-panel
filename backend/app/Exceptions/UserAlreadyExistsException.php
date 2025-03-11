<?php
namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use App\Http\Responses\Api\ApiResponse;
use App\Enums\HttpStatus;

class UserAlreadyExistsException extends Exception {
    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request): ApiResponse {
        return new ApiResponse(
            status: HttpStatus::CONFLICT,
            message: __('auth.userAlreadyExists')
        );
    }
}
