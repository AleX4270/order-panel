<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole {
    public function translation(): HasOne {
        return $this->hasOne(RoleTranslation::class)
            ->whereHas('language', function($query) {
                $query->where('symbol', app()->getLocale());
            });
    }
}
