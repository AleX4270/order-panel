<?php
namespace App\Exceptions\Nominatim\Geocoding;

use Illuminate\Http\Request;
use App\Http\Responses\Api\ApiResponse;
use App\Enums\HttpStatus;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class CoordinatesNotFoundException extends RuntimeException {
    public function report(): void {
        Log::error($this->message);
    }

    public function render(Request $request): ApiResponse {
        return new ApiResponse(
            status: HttpStatus::INTERNAL_SERVER_ERROR,
            message: __('response.coordinatesNotFound')
        );
    }
}
