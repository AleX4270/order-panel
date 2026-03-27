<?php
declare(strict_types=1);

namespace App\Enums;

enum NotificationEventType: string {
    case ORDER_COMPLETED = 'order_completed';
    case INCOMING_ORDER_DEADLINE = 'incoming_order_deadline';
}
