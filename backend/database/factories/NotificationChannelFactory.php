<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Language;
use App\Models\NotificationChannel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NotificationChannel>
 */
class NotificationChannelFactory extends Factory {
    protected $model = NotificationChannel::class;

    public function definition(): array {
        return [
            'symbol' => fake()->unique()->word(),
            'is_active' => true,
        ];
    }

    public function configure(): static {
        return $this->afterCreating(function(NotificationChannel $notificationChannel) {
            $notificationChannel->translations()->create([
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
