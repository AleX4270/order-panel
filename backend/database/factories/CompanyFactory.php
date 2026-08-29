<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory {
    public function definition(): array {
        return [
            'name' => fake()->company(),
            'address_id' => Address::factory(),
        ];
    }
}
