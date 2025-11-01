<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Language;
use App\Models\OrderStatus;
use Illuminate\Support\Facades\DB;

class OrderStatusSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        DB::transaction(function() {
            $statuses = $this->getStatuses();
            foreach($statuses as $status) {
                $newStatus = OrderStatus::updateOrCreate(
                    ['symbol' => $status['symbol']],
                    [
                        'symbol' => $status['symbol'],
                        'is_internal' => $status['is_internal'],
                        'is_active' => $status['is_active'],
                    ],
                );
    
                foreach($status['translations'] as $language => $translation) {
                    $languageId = Language::where('symbol', $language)->first()?->id;
    
                    $newStatus->translations()->updateOrCreate(
                        [
                            'language_id' => $languageId,
                        ],
                        [
                            'order_status_id' => $newStatus->id,
                            'language_id' => $languageId,
                            'name' => $translation
                        ]
                    );
                }
            }
        });
    }

    public function getStatuses(): array {
        return [
            [
                'symbol' => 'in_progress',
                'is_internal' => 1,
                'is_active' => 1,
                'translations' => [
                    'pl' => 'W trakcie realizacji',
                    'en' => 'In progress',
                ]
            ],
            [
                'symbol' => 'completed',
                'is_internal' => 1,
                'is_active' => 1,
                'translations' => [
                    'pl' => 'Zakończony',
                    'en' => 'Completed',
                ]
            ]
        ];
    }
}
