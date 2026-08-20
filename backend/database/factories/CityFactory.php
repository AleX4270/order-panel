<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Province;
use Illuminate\Database\Eloquent\Factories\Factory;

class CityFactory extends Factory {
    public function definition(): array {
        return [
            'province_id' => Province::factory(),
            'name' => fake()->city(),
        ];
    }
}
