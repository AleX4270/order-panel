<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Country;
use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\Factory;

class CountryFactory extends Factory {
    public function definition(): array {
        return [
            'symbol' => fake()->countryCode(),
        ];
    }

    public function configure(): static {
        return $this->afterCreating(function(Country $country) {
            $country->translations()->create([
                'language_id' => Language::where('symbol', app()->getLocale())->firstOrFail()->id,
                'name' => fake()->country(),
            ]);
        });
    }
}
