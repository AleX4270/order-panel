<?php
declare(strict_types=1);

use App\Enums\OrderStatusType;
use App\Models\OrderStatus;

function createCompletedOrderStatus(): OrderStatus {
    return OrderStatus::factory()->create(['symbol' => OrderStatusType::COMPLETED->value]);
}
