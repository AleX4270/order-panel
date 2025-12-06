<?php
declare(strict_types=1);

namespace App\Enums;

enum RoleType: string {
    case ADMIN = 'admin';
    case MANAGER = 'manager';
    case WORKER = 'worker';
}
