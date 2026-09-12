<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Address;
use App\Models\Client;
use App\Models\Language;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Priority;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory {
    public function definition(): array {
        return [
            'symbol' => Str::random(16),
            'date_deadline' => now()->addDays(14),
            'user_creation_id' => User::factory(),
            'priority_id' => Priority::factory(),
            'client_id' => Client::factory(),
            'status_id' => OrderStatus::factory(),
            'address_id' => Address::factory(),
        ];
    }

    public function configure(): static {
        return $this
            ->afterMaking(function(Order $order) {
                $order->user_modification_id ??= $order->user_creation_id;
            })
            ->afterCreating(function(Order $order) {
                $order->translations()->create([
                    'language_id' => Language::where('symbol', app()->getLocale())->firstOrFail()->id,
                    'remarks' => fake()->sentence(),
                ]);
            });
    }

    public function inactive(): static {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }
}
