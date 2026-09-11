<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Address;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderRequestFactory extends Factory {
    public function definition(): array {
        return [
            'client_id' => Client::factory(),
            'address_id' => Address::factory(),
            'remarks' => fake()->sentence(),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'consent_given_at' => now(),
        ];
    }
}
