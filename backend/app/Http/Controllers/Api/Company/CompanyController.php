<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\Company;

use App\Enums\HttpStatus;
use App\Http\Requests\Api\Company\CompanyRequest;
use App\Http\Resources\Api\Company\CompanyResource;
use App\Http\Responses\Api\ApiResponse;
use App\Services\Api\Company\CompanyService;
use Illuminate\Http\Request;

class CompanyController {
    public function __construct(
        private readonly CompanyService $companyService,
    ) {}

    public function show(Request $request): ApiResponse {
        $result = $this->companyService->show();

        return new ApiResponse(
            data: CompanyResource::make($result),
            status: HttpStatus::OK,
            message: __('response.success'),
        );
    }

    public function update(CompanyRequest $request): ApiResponse {
        $this->companyService->update($request->toDto());

        return new ApiResponse(
            status: HttpStatus::OK,
            message: __('response.success'),
        );
    }
}
