<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model {
    protected $table = 'city';

    protected $fillable = [
        'province_id', 
        'postal_code', 
        'name'
    ];

    public function province(): BelongsTo {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function addresses(): HasMany {
        return $this->hasMany(Address::class, 'city_id');
    }
}
