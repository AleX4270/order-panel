<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\Province;

use App\Enums\HttpStatus;
use App\Http\Requests\Api\Province\ProvinceFilterRequest;
use App\Http\Responses\Api\ApiResponse;
use App\Services\Api\Province\ProvinceService;

class ProvinceController {
    public function __construct(
        private readonly ProvinceService $provinceService,
    ) {}

    public function index(ProvinceFilterRequest $request): ApiResponse {
        $data = $this->provinceService->index($request->toDto());

        return new ApiResponse(
            data: $data,
            status: HttpStatus::OK,
            message: __('response.success'),
        );
    }
}
