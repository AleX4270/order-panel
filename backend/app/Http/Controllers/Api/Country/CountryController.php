<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\Country;

use App\Enums\HttpStatus;
use App\Http\Requests\Api\Country\CountryFilterRequest;
use App\Http\Responses\Api\ApiResponse;
use App\Services\Api\Country\CountryService;

class CountryController {
    public function __construct(
        private readonly CountryService $countryService,
    ) {}

    public function index(CountryFilterRequest $request): ApiResponse {
        $data = $this->countryService->index($request->toDto());

        return new ApiResponse(
            data: $data,
            status: HttpStatus::OK,
            message: __('response.success'),
        );
    }
}
