<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model {
    protected $attributes = [
        'is_active' => 1
    ];

    protected $fillable = ['symbol', 'name', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean'
    ];
}
