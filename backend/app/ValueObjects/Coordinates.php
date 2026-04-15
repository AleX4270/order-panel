<?php
declare(strict_types=1);

namespace App\ValueObjects;

final readonly class Coordinates {
    public function __construct(
        public float $latitude,
        public float $longitude,
    ) {}
}