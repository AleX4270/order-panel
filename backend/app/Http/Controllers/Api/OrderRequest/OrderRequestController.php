<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\OrderRequest;

use App\Enums\HttpStatus;
use App\Http\Requests\Api\OrderRequest\OrderRequestCastToOrderRequest;
use App\Http\Requests\Api\OrderRequest\OrderRequestDeleteRequest;
use App\Http\Requests\Api\OrderRequest\OrderRequestFilterRequest;
use App\Http\Requests\Api\OrderRequest\OrderRequestRequest;
use App\Http\Resources\Api\OrderRequest\OrderRequestResource;
use App\Http\Responses\Api\ApiResponse;
use App\Services\Api\OrderRequest\OrderRequestService;

class OrderRequestController {
    public function __construct(
        private readonly OrderRequestService $orderRequestService,
    ) {}

    public function index(OrderRequestFilterRequest $request): ApiResponse {
        $result = $this->orderRequestService->index($request->toDto());

        return new ApiResponse(
            data: [
                'items' => OrderRequestResource::collection($result['items']),
                'count' => $result['count'],
            ],
            status: HttpStatus::OK,
            message: __('response.success'),
        );
    }

    public function store(OrderRequestRequest $request): ApiResponse {
        $this->orderRequestService->store($request->toDto());

        return new ApiResponse(
            status: HttpStatus::CREATED,
            message: __('response.created'),
        );
    }

    public function castToOrder(OrderRequestCastToOrderRequest $request): ApiResponse {
        $this->orderRequestService->castToOrder($request->getOrderRequestId());

        return new ApiResponse(
            status: HttpStatus::CREATED,
            message: __('response.created'),
        );
    }

    public function delete(OrderRequestDeleteRequest $request): ApiResponse {
        $this->orderRequestService->delete($request->getOrderRequestId());

        return new ApiResponse(
            status: HttpStatus::OK,
            message: __('response.deleted'),
        );
    }
}
