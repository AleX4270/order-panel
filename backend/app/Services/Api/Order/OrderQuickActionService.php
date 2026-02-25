<?php
declare(strict_types=1);

namespace App\Services\Api\Order;

use App\Enums\OrderStatusType;
use App\Exceptions\Api\OrderQuickAction\OrderAlreadyCompletedException;
use App\Exceptions\Api\OrderStatus\OrderStatusNotFoundException;
use App\Models\Order;
use App\Models\OrderStatus;

class OrderQuickActionService {

    public function markAsCompleted(int $orderId): void {
        $completedStatusId = OrderStatus::where('symbol', OrderStatusType::COMPLETED->value)->first()?->id;

        if(empty($completedStatusId)) {
            throw new OrderStatusNotFoundException("An order status with the symbol: " . OrderStatusType::COMPLETED->value . " could not be found");
        }
    
        $order = Order::findOrFail($orderId);

        if($order->status_id === $completedStatusId && !empty($order->date_completed)) {
            throw new OrderAlreadyCompletedException("Processed order is already marked as completed");
        }

        $order->status_id = $completedStatusId;
        $order->date_completed = now();

        $order->saveOrFail();
    }
}
