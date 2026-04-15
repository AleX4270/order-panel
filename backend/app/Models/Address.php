<?php
declare(strict_types=1);

namespace App\Models;

use App\Casts\AsCoordinates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Address extends Model {
    protected $fillable = [
        'city_id', 
        'address',
        'postal_code',
        'coordinates',
    ];

    public function clients(): HasMany {
        return $this->hasMany(Client::class, 'address_id');
    }

    public function city(): BelongsTo {
        return $this->belongsTo(City::class, 'city_id');
    }

    protected function casts(): array {
        return [
            'coordinates' => AsCoordinates::class,
        ];
    }
}
