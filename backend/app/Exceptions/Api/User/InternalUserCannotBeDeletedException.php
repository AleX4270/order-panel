<?php
declare(strict_types=1);

namespace App\Exceptions\Api\User;

use App\Enums\HttpStatus;
use App\Http\Responses\Api\ApiResponse;
use Illuminate\Http\Request;
use RuntimeException;

class InternalUserCannotBeDeletedException extends RuntimeException {
    public function render(Request $request): ApiResponse {
        return new ApiResponse(
            status: HttpStatus::FORBIDDEN,
            message: __('response.internalUserCannotBeDeleted'),
        );
    }
}
