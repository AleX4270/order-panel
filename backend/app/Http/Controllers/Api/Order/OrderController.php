<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\Order;

use App\Enums\HttpStatus;
use App\Http\Requests\Api\Order\OrderRequest;
use App\Http\Responses\Api\ApiResponse;
use App\Services\Api\Order\OrderService;

class OrderController {
    public function __construct(
        private readonly OrderService $orderService,
    ) {}

    // TODO: Add the FormRequest
    public function index(): ApiResponse {
        $result = $this->orderService->index();

        return new ApiResponse(
            data: $result,
            status: HttpStatus::OK,
            message: __('response.success'),
        );
    }

    public function store(OrderRequest $request): ApiResponse {
        $this->orderService->store($request->toDto());

        return new ApiResponse(
            status: HttpStatus::CREATED,
            message: __('response.created'),
        );
    }

    public function update(OrderRequest $request): ApiResponse {
        $this->orderService->update($request->toDto());

        return new ApiResponse(
            status: HttpStatus::NO_CONTENT,
            message: __('response.success'),
        );
    }

    public function destroy() {
        //
    }
}
