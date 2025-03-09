<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Language extends Model {
    protected $table = 'language';

    protected $attributes = [
        'is_active' => 1
    ];

    protected $fillable = [
        'symbol', 
        'name', 
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function scopeActive(Builder $query): void {
        $query->where('is_active', 1);
    }
}
