<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\Language;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        DB::transaction(function() {
            $countries = $this->getCountries();
            foreach($countries as $country) {
                $newCountry = Country::updateOrCreate(
                    ['symbol' => $country['symbol']],
                    ['symbol' => $country['symbol']],
                );
    
                foreach($country['translations'] as $language => $translation) {
                    $languageId = Language::where('symbol', $language)->first()?->id;
    
                    $newCountry->translations()->updateOrCreate(
                        [
                            'language_id' => $languageId,
                        ],
                        [
                            'country_id' => $newCountry->id,
                            'language_id' => $languageId,
                            'name' => $translation
                        ]
                    );
                }
            }
        });
    }

    public function getCountries(): array {
        return [
            [
                'symbol' => 'PL',
                'translations' => [
                    'pl' => 'Polska',
                    'en' => 'Poland',
                ]
            ]
        ];
    }
}
