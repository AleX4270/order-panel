<?php
declare(strict_types=1);

namespace App\Enums;

enum NotificationChannelType: string {
    case BROADCAST = 'broadcast';
    case MAIL = 'mail';
}
