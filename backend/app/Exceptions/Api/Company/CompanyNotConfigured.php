<?php
declare(strict_types=1);

namespace App\Exceptions\Api\Company;

use App\Enums\HttpStatus;
use App\Http\Responses\Api\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CompanyNotConfigured extends Exception {
    public function render(Request $request): ApiResponse {
        return new ApiResponse(
            status: HttpStatus::NOT_FOUND,
            message: __('response.companyNotConfigured'),
        );
    }
}
