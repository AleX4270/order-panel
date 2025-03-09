<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Order extends Model {
    protected $table = 'order';

    protected $attributes = [
        'is_active' => 1
    ];

    protected $fillable = [
        'date_deadline',
        'date_completed',
        'user_creation_id',
        'user_modification_id',
        'priority_id',
        'client_id',
        'order_status_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function scopeActive(Builder $query): void {
        $query->where('is_active', 1);
    }
}
