<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\City;

use App\Enums\HttpStatus;
use App\Http\Requests\Api\City\CityFilterRequest;
use App\Http\Responses\Api\ApiResponse;
use App\Services\Api\City\CityService;

class CityController {
    public function __construct(
        private readonly CityService $cityService,
    ) {}

    public function index(CityFilterRequest $request): ApiResponse {
        $data = $this->cityService->index($request->toDto());

        return new ApiResponse(
            data: $data,
            status: HttpStatus::OK,
            message: __('response.success'),
        );
    }
}
