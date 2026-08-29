<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\City;
use App\ValueObjects\Coordinates;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory {
    public function definition(): array {
        return [
            'city_id' => City::factory(),
            'address' => fake()->streetAddress(),
            'postal_code' => fake()->postcode(),
            'coordinates' => new Coordinates(
                fake()->latitude(),
                fake()->longitude(),
            ),
        ];
    }
}
