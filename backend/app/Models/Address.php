<?php
declare(strict_types=1);

namespace App\Models;

use App\ValueObjects\Coordinates;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class Address extends Model {
    protected $fillable = [
        'city_id', 
        'address',
        'postal_code',
        'coordinates',
    ];

    public function companies(): HasMany {
        return $this->hasMany(Company::class, 'address_id');
    }

    public function orders(): HasMany {
        return $this->hasMany(Order::class, 'address_id');
    }

    public function city(): BelongsTo {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function coordinates(): Attribute {
        return Attribute::set(function($value) {
            if(!$value instanceof Coordinates) {
                throw new InvalidArgumentException('The given value is not a Coordinates instance.');
            }

            return DB::raw(sprintf('ST_MakePoint(%f, %f)::geography', $value->longitude, $value->latitude));
        });
    }

    public function longitude(): Attribute {
        return Attribute::get(function() {
            return (float)$this->query()
                ->whereKey($this)
                ->selectRaw('ST_X(coordinates::geometry) as longitude')
                ->toBase()
                ->soleValue('longitude');
        });
    }

    public function latitude(): Attribute {
        return Attribute::get(function() {
            return (float)$this->query()
                ->whereKey($this)
                ->selectRaw('ST_Y(coordinates::geometry) as latitude')
                ->toBase()
                ->soleValue('latitude');
        });
    }
}
