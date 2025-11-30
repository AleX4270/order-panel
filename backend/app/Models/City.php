<?php
declare(strict_types=1);

namespace App\Models;

use App\Traits\ConvertsModelKeysToCamelCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model {
    use ConvertsModelKeysToCamelCase;

    protected $fillable = [
        'province_id', 
        'name'
    ];

    public function province(): BelongsTo {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function addresses(): HasMany {
        return $this->hasMany(Address::class, 'city_id');
    }
}
