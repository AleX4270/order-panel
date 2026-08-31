<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use App\Notifications\IncomingOrderDeadlineNotification;
use App\Notifications\OrderCompletedNotification;
use App\Notifications\OrderRequestCreatedNotification;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @extends Factory<DatabaseNotification>
 */
class NotificationFactory extends Factory {
    protected $model = DatabaseNotification::class;

    public function definition(): array {
        return [
            'id' => Str::uuid()->toString(),
            'type' => fake()->randomElement([
                OrderCompletedNotification::class,
                OrderRequestCreatedNotification::class,
                IncomingOrderDeadlineNotification::class,
            ]),
            'notifiable_type' => User::class,
            'notifiable_id' => User::factory(),
            'data' => [
                'title' => fake()->sentence(3),
                'message' => fake()->sentence(),
                'readAt' => null, //TODO: This is useless if there already is that type of field in the notification entity
                'createdAt' => Carbon::now()->toISOString(), //TODO: This too?
            ],
            'read_at' => null,
        ];
    }

    public function read(): static {
        return $this->state(fn(array $attributes) => [
            'read_at' => Carbon::now(),
        ]);
    }
}
