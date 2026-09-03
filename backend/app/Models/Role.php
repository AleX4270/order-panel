<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole {
    use HasFactory;

    public function translation(): HasOne {
        return $this->hasOne(RoleTranslation::class)
            ->whereHas('language', function($query) {
                $query->where('symbol', app()->getLocale());
            });
    }

    public function translations(): HasMany {
        return $this->hasMany(RoleTranslation::class, 'role_id');
    }
}
