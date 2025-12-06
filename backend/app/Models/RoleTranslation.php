<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Models\Role;

class RoleTranslation extends Model {
    protected $fillable = [
        'role_id', 
        'language_id', 
        'name',
    ];

    public function language(): BelongsTo {
        return $this->belongsTo(Language::class, 'language_id');
    }

    public function role(): BelongsTo {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
