<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Language;

class LanguageSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $languages = $this->getLanguages();

        foreach($languages as $language) {
            Language::updateOrCreate(
                ['symbol' => $language['symbol']], 
                $language
            );
        }
    }

    public function getLanguages(): array {
        return [
            [
                'symbol' => 'pl',
                'name' => 'Polski',
                'is_active' => 1,
            ],
            [
                'symbol' => 'en',
                'name' => 'English',
                'is_active' => 1,
            ]
        ];
    }
}
