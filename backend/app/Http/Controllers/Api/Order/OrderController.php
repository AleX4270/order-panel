<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\Order;

use App\Enums\HttpStatus;
use App\Http\Requests\Api\Order\OrderDeleteRequest;
use App\Http\Requests\Api\Order\OrderFilterRequest;
use App\Http\Requests\Api\Order\OrderRequest;
use App\Http\Requests\Api\Order\OrderShowRequest;
use App\Http\Resources\Api\Order\OrderResource;
use App\Http\Responses\Api\ApiResponse;
use App\Services\Api\Order\OrderService;

class OrderController {
    public function __construct(
        private readonly OrderService $orderService,
    ) {}

    public function index(OrderFilterRequest $request): ApiResponse {
        $result = $this->orderService->index($request->toDto());

        return new ApiResponse(
            data: [
                'items' => OrderResource::collection($result['items']),
                'count' => $result['count'],
            ],
            status: HttpStatus::OK,
            message: __('response.success'),
        );
    }

    public function show(OrderShowRequest $request): ApiResponse {
        $result = $this->orderService->show($request->getOrderId());

        return new ApiResponse(
            data: OrderResource::make($result),
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

    public function delete(OrderDeleteRequest $request): ApiResponse {
        $this->orderService->delete($request->getOrderId());

        return new ApiResponse(
            status: HttpStatus::OK,
            message: __('response.deleted'),
        );
    }
}
