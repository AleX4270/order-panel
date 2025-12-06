<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\Order;

use App\Enums\HttpStatus;
use App\Enums\PermissionType;
use App\Http\Requests\Api\Order\OrderFilterRequest;
use App\Http\Requests\Api\Order\OrderRequest;
use App\Http\Responses\Api\ApiResponse;
use App\Services\Api\Order\OrderService;
use Illuminate\Http\Request;

class OrderController {
    public function __construct(
        private readonly OrderService $orderService,
    ) {}

    public function index(OrderFilterRequest $request): ApiResponse {
        $result = $this->orderService->index($request->toDto());

        return new ApiResponse(
            data: $result,
            status: HttpStatus::OK,
            message: __('response.success'),
        );
    }

    public function show(Request $request): ApiResponse {
        if(!$request->user()?->can(PermissionType::ORDERS_SHOW->value)) {
            return new ApiResponse(
                status: HttpStatus::UNAUTHORIZED,
                message: __('response.unauthorized'),
            );       
        }

        $orderId = (int)$request->route('id');

        if(empty($orderId)) {
            return new ApiResponse(
                status: HttpStatus::BAD_REQUEST,
                message: __('response.badRequest'),
            );    
        }

        $result = $this->orderService->show($orderId);

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

    public function delete(Request $request) {
        if(!$request->user()?->can(PermissionType::ORDERS_DELETE->value)) {
            return new ApiResponse(
                status: HttpStatus::UNAUTHORIZED,
                message: __('response.unauthorized'),
            );
        }

        $orderId = (int)$request->route('id');

        if(empty($orderId)) {
            return new ApiResponse(
                status: HttpStatus::BAD_REQUEST,
                message: __('response.badRequest'),
            );    
        }

        $this->orderService->delete($orderId);

        return new ApiResponse(
            status: HttpStatus::OK,
            message: __('response.deleted'),
        );
    }
}
