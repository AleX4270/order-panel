<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait ConvertsModelKeysToCamelCase {
    public function toCamelCaseKeys(): Collection {
        return collect($this->getAttributes())
            ->mapWithKeys(fn($value, $key) => [Str::camel($key) => $value]);
    }
}