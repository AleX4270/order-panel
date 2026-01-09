<?php
declare(strict_types=1);

namespace App\Enums;

enum PermissionType: string {
    //Orders
    case ORDERS_VIEW = 'orders.view';
    case ORDERS_SHOW = 'orders.show';
    case ORDERS_CREATE = 'orders.create';
    case ORDERS_UPDATE = 'orders.update';
    case ORDERS_DELETE = 'orders.delete';

    //Users
    case USERS_VIEW = 'users.view';
    case USERS_SHOW = 'users.show';
    case USERS_CREATE = 'users.create';
    case USERS_UPDATE = 'users.update';
    case USERS_DELETE = 'users.delete';

    //Dashboard
    case DASHBOARD_VIEW = 'dashboard.view';

    public static function all(): array {
        $permissions = [];
        
        foreach(self::cases() as $permission) {
            $permissions[] = $permission->value;
        }

        return $permissions;
    }
}
