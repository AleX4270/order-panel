<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\Order;

use App\Enums\HttpStatus;
use App\Http\Responses\Api\ApiResponse;
use App\Services\Api\Order\OrderService;

class OrderController {
    public function __construct(
        private readonly OrderService $orderService,
    ) {}

    // TODO: Add the FormRequest
    public function index(): ApiResponse {
        $data = $this->orderService->index();

        return new ApiResponse(
            data: $data,
            status: HttpStatus::OK,
            message: __('response.success'),
        );
    }

    public function store() {
        //
    }

    public function update() {
        //
    }

    public function destroy() {
        //
    }
}
