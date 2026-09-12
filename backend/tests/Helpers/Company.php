<?php
declare(strict_types=1);

use App\Models\Address;
use App\Models\Company;
use App\ValueObjects\Coordinates;

function createHeadquarters(float $latitude = 52.0, float $longitude = 21.0): Company {
    return Company::factory()
        ->for(Address::factory()->state([
            'coordinates' => new Coordinates($latitude, $longitude),
        ]))
        ->create();
}
