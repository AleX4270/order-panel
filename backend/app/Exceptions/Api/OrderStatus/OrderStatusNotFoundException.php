<?php
namespace App\Exceptions\Api\OrderStatus;

use Illuminate\Http\Request;
use App\Http\Responses\Api\ApiResponse;
use App\Enums\HttpStatus;
use RuntimeException;

class OrderStatusNotFoundException extends RuntimeException {
    public function render(Request $request): ApiResponse {
        return new ApiResponse(
            status: HttpStatus::INTERNAL_SERVER_ERROR,
            message: __('response.internalError')
        );
    }
}
