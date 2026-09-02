<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Language;
use App\Models\NotificationEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NotificationEvent>
 */
class NotificationEventFactory extends Factory {
    protected $model = NotificationEvent::class;

    public function definition(): array {
        return [
            'symbol' => fake()->unique()->word(),
            'is_active' => true,
        ];
    }

    public function configure(): static {
        return $this->afterCreating(function(NotificationEvent $notificationEvent) {
            $notificationEvent->translations()->create([
                'language_id' => Language::where('symbol', app()->getLocale())->firstOrFail()->id,
                'name' => fake()->word(),
            ]);
        });
    }
}
