<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Language;
use App\Models\Priority;
use Illuminate\Support\Facades\DB;

class PrioritySeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        DB::transaction(function() {
            $priorities = $this->getPriorityLevels();
            foreach($priorities as $priority) {
                $newPriority = Priority::updateOrCreate(
                    ['symbol' => $priority['symbol']],
                    [
                        'symbol' => $priority['symbol'],
                        'is_active' => $priority['is_active'],
                    ],
                );
    
                foreach($priority['translations'] as $language => $translation) {
                    $languageId = Language::where('symbol', $language)->first()?->id;
    
                    $newPriority->translations()->updateOrCreate(
                        [
                            'language_id' => $languageId,
                        ],
                        [
                            'priority_id' => $newPriority->id,
                            'language_id' => $languageId,
                            'name' => $translation
                        ]
                    );
                }
            }
        });
    }

    public function getPriorityLevels(): array {
        return [
            [
                'symbol' => 'low',
                'is_active' => 1,
                'translations' => [
                    'pl' => 'Niski',
                    'en' => 'Low',
                ]
            ],
            [
                'symbol' => 'medium',
                'is_active' => 1,
                'translations' => [
                    'pl' => 'Średni',
                    'en' => 'Medium',
                ]
            ],
            [
                'symbol' => 'high',
                'is_active' => 1,
                'translations' => [
                    'pl' => 'Wysoki',
                    'en' => 'High',
                ]
            ],
        ];
    }
}
