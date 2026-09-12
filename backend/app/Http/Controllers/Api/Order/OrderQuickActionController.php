<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\Order;

use App\Enums\HttpStatus;
use App\Http\Requests\Api\Order\OrderMarkAsCompletedRequest;
use App\Http\Responses\Api\ApiResponse;
use App\Services\Api\Order\OrderQuickActionService;

class OrderQuickActionController {
    public function __construct(
        private readonly OrderQuickActionService $orderQuickActionService,
    ) {}

    public function markAsCompleted(OrderMarkAsCompletedRequest $request): ApiResponse {
        $this->orderQuickActionService->markAsCompleted($request->getOrderId());

        return new ApiResponse(
            status: HttpStatus::NO_CONTENT,
            message: __('response.success'),
        );
    }
}
