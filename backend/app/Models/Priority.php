<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Priority extends Model {
    protected $table = 'priority';

    protected $attributes = [
        'is_active' => 1
    ];

    protected $fillable = [
        'symbol', 
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function scopeActive(Builder $query): void {
        $query->where('is_active', 1);
    }
}
