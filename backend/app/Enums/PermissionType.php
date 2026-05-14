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
    case ORDERS_MARK_AS_COMPLETED = 'orders.mark_as_completed';

    //Order Requests
    case ORDER_REQUESTS_VIEW = 'order_requests.view';
    case ORDER_REQUESTS_SHOW = 'order_requests.show';
    case ORDER_REQUESTS_MANAGE = 'order_requests.manage'; //TODO: Migrate other permissions to manage instead of create / update too.

    //Users
    case USERS_VIEW = 'users.view';
    case USERS_SHOW = 'users.show';
    case USERS_CREATE = 'users.create';
    case USERS_UPDATE = 'users.update';
    case USERS_DELETE = 'users.delete';

    //Dashboard
    case DASHBOARD_VIEW = 'dashboard.view';

    //Company
    case COMPANY_MANAGE = 'company.manage';

    public static function all(): array {
        $permissions = [];
        
        foreach(self::cases() as $permission) {
            $permissions[] = $permission->value;
        }

        return $permissions;
    }
}
