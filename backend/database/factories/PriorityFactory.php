<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Language;
use App\Models\Priority;
use Illuminate\Database\Eloquent\Factories\Factory;

class PriorityFactory extends Factory {
    public function definition(): array {
        return [
            'symbol' => fake()->unique()->word(),
            'is_active' => true,
        ];
    }

    public function configure(): static {
        return $this->afterCreating(function(Priority $priority) {
            $priority->translations()->create([
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
