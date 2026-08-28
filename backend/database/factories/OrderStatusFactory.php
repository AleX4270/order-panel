<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Language;
use App\Models\OrderStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderStatusFactory extends Factory {
    public function definition(): array {
        return [
            'symbol' => fake()->unique()->word(),
            'is_internal' => false,
            'is_active' => true,
        ];
    }

    public function configure(): static {
        return $this->afterCreating(function(OrderStatus $orderStatus) {
            $orderStatus->translations()->create([
                'language_id' => Language::where('symbol', app()->getLocale())->firstOrFail()->id,
                'name' => fake()->word(),
            ]);
        });
    }

    public function inactive(): static {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }
}
