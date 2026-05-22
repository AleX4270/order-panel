<?php
declare(strict_types=1);

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\User;

class OrderRequestPolicy {
    public function castToOrder(User $user): bool {
        return $user->can(PermissionType::ORDER_REQUESTS_MANAGE->value);
    }

    public function delete(User $user): bool {
        return $user->can(PermissionType::ORDER_REQUESTS_MANAGE->value);
    }
}
