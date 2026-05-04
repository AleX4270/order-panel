<?php
declare(strict_types=1);

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\User;

class CompanyPolicy {
    public function show(User $user): bool {
        return $user->can(PermissionType::COMPANY_MANAGE->value);
    }
}
