<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\OrderRequest;

use App\Enums\HttpStatus;
use App\Http\Requests\Api\OrderRequest\OrderRequestRequest;
use App\Http\Responses\Api\ApiResponse;
use App\Services\Api\OrderRequest\OrderRequestService;

class OrderRequestController {
    public function __construct(
        private readonly OrderRequestService $orderRequestService,
    ) {}

    public function store(OrderRequestRequest $request): ApiResponse {
        $this->orderRequestService->store($request->toDto());

        return new ApiResponse(
            status: HttpStatus::CREATED,
            message: __('response.created'),
        );
    }
}
