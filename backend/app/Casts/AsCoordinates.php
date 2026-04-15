<?php

namespace App\Casts;

use App\ValueObjects\Coordinates;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class AsCoordinates implements CastsAttributes {
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed {
        return $value;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): mixed {
        if(!$value instanceof Coordinates) {
            throw new InvalidArgumentException('The given value is not a Coordinates instance.');
        }

        return [
            'coordinates' => DB::raw(sprintf('ST_MakePoint(%f, %f)::geography', $value->longitude, $value->latitude)),
        ];
    }
}
