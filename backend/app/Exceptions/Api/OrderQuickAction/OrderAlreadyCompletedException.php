<?php
namespace App\Exceptions\Api\OrderQuickAction;

use Illuminate\Http\Request;
use App\Http\Responses\Api\ApiResponse;
use App\Enums\HttpStatus;
use RuntimeException;

class OrderAlreadyCompletedException extends RuntimeException {
    public function render(Request $request): ApiResponse {
        return new ApiResponse(
            status: HttpStatus::BAD_REQUEST,
            message: __('response.orderAlreadyCompleted')
        );
    }
}
