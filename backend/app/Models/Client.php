<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model {
    protected $table = 'client';

    protected $attributes = [
        'is_blocked' => 0,
        'is_active' => 1
    ];

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'address_id',
        'is_blocked',
        'is_active',
    ];

    protected $casts = [
        'is_blocked' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function scopeActive(Builder $query): void {
        $query->where('is_active', 1);
    }

    public function scopeBlocked(Builder $query): void {
        $query->where('is_blocked', 1);
    }

    public function scopeAllowed(Builder $query): void {
        $query->where('is_blocked', 0);
    }

    public function orders(): HasMany {
        return $this->hasMany(Order::class, 'client_id');
    }
}
