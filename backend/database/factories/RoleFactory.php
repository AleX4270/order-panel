<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Language;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory {
    protected $model = Role::class;

    public function definition(): array {
        return [
            'name' => fake()->unique()->word(),
            'guard_name' => 'web',
        ];
    }

    public function configure(): static {
        return $this->afterCreating(function(Role $role) {
            $role->translations()->create([
                'language_id' => Language::where('symbol', app()->getLocale())->firstOrFail()->id,
                'name' => fake()->word(),
            ]);
        });
    }
}
