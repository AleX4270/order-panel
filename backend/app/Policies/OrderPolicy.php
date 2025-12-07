<?php
declare(strict_types=1);

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\User;

class OrderPolicy {
    public function show(User $user): bool {
        return $user->can(PermissionType::ORDERS_SHOW->value);
    }

    public function delete(User $user): bool {
        return $user->can(PermissionType::ORDERS_DELETE->value);
    }
}
