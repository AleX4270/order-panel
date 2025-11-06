<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\Priority;

use App\Enums\HttpStatus;
use App\Http\Requests\Api\Priority\PriorityFilterRequest;
use App\Http\Responses\Api\ApiResponse;
use App\Services\Api\Priority\PriorityService;

class PriorityController {
    public function __construct(
        private readonly PriorityService $priorityService,
    ) {}

    public function index(PriorityFilterRequest $request): ApiResponse {
        $data = $this->priorityService->index($request->toDto());

        return new ApiResponse(
            data: $data,
            status: HttpStatus::OK,
            message: __('response.success'),
        );
    }
}
