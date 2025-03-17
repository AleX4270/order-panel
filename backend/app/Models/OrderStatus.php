<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderStatus extends Model {
    protected $table = 'order_status';

    protected $attributes = [
        'is_internal' => 0,
        'is_active' => 1
    ];

    protected $fillable = [
        'symbol', 
        'is_internal', 
        'is_active'
    ];

    protected $casts = [
        'is_internal' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function scopeActive(Builder $query): void {
        $query->where('is_active', 1);
    }

    public function showInternal(Builder $query): void {
        $query->where('is_internal', 1);
    }

    public function orders(): HasMany {
        return $this->hasMany(Order::class, 'order_status_id');
    }
}
