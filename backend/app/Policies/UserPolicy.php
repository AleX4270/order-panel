<?php
declare(strict_types=1);

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\User;

class UserPolicy {
    public function delete(User $user, User $model): bool {
        return $user->can(PermissionType::USERS_DELETE->value) && !$model->is_internal;
    }
}
