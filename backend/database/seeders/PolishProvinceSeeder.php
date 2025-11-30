<?php

namespace Database\Seeders;

use App\Enums\CountryType;
use App\Models\Country;
use Illuminate\Database\Seeder;
use App\Models\Province;
use Illuminate\Support\Facades\DB;

class PolishProvinceSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        DB::transaction(function() {
            $provinces = $this->getProvinces();
            foreach($provinces as $province) {
                $countryId = Country::where('symbol', $province['countrySymbol'])->first()?->id;

                Province::insert(
                    [
                        'country_id' => $countryId,
                        'name' => $province['name']
                    ],
                );
            }
        });
    }

    public function getProvinces(): array {
        return [
            [
                'countrySymbol' => CountryType::PL->value,
                'name' => 'Wielkopolskie',
            ],
            [
                'countrySymbol' => CountryType::PL->value,
                'name' => 'Kujawsko-pomorskie',
            ],
            [
                'countrySymbol' => CountryType::PL->value,
                'name' => 'Małopolskie',
            ],
            [
                'countrySymbol' => CountryType::PL->value,
                'name' => 'Łódzkie',
            ],
            [
                'countrySymbol' => CountryType::PL->value,
                'name' => 'Dolnośląskie',
            ],
            [
                'countrySymbol' => CountryType::PL->value,
                'name' => 'Lubelskie',
            ],
            [
                'countrySymbol' => CountryType::PL->value,
                'name' => 'Lubuskie',
            ],
            [
                'countrySymbol' => CountryType::PL->value,
                'name' => 'Mazowieckie',
            ],
            [
                'countrySymbol' => CountryType::PL->value,
                'name' => 'Opolskie',
            ],
            [
                'countrySymbol' => CountryType::PL->value,
                'name' => 'Podlaskie',
            ],
            [
                'countrySymbol' => CountryType::PL->value,
                'name' => 'Pomorskie',
            ],
            [
                'countrySymbol' => CountryType::PL->value,
                'name' => 'Śląskie',
            ],
            [
                'countrySymbol' => CountryType::PL->value,
                'name' => 'Podkarpackie',
            ],
            [
                'countrySymbol' => CountryType::PL->value,
                'name' => 'Świętokrzyskie',
            ],
            [
                'countrySymbol' => CountryType::PL->value,
                'name' => 'Warmińsko-Mazurskie',
            ],
            [
                'countrySymbol' => CountryType::PL->value,
                'name' => 'Zachodniopomorskie',
            ],
        ];
    }
}
