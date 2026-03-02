<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\Order;

use App\Enums\HttpStatus;
use App\Http\Responses\Api\ApiResponse;
use App\Models\Order;
use App\Services\Api\Order\OrderQuickActionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OrderQuickActionController {
    public function __construct(
        private readonly OrderQuickActionService $orderQuickActionService,
    ) {}

    public function markAsCompleted(Request $request): ApiResponse {
        Gate::authorize('markAsCompleted', Order::class);

        $orderId = $request->integer('id');
        if(empty($orderId)) {
            return new ApiResponse(
                status: HttpStatus::BAD_REQUEST,
                message: __('response.badRequest'),
            );
        }

        $this->orderQuickActionService->markAsCompleted($orderId);

        return new ApiResponse(
            status: HttpStatus::NO_CONTENT,
            message: __('response.success'),
        );
    }
}
