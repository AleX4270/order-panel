<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Language;
use App\Models\NotificationEvent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationEventSeeder extends Seeder {
    public function run(): void {
        DB::transaction(function() {
            $events = $this->getEvents();
            foreach($events as $event) {
                $newEvent = NotificationEvent::updateOrCreate(
                    ['symbol' => $event['symbol']],
                    ['symbol' => $event['symbol']],
                );
    
                foreach($event['translations'] as $language => $translation) {
                    $languageId = Language::where('symbol', $language)->first()?->id;
    
                    $newEvent->translations()->updateOrCreate(
                        [
                            'language_id' => $languageId,
                        ],
                        [
                            'notification_event_id' => $newEvent->id,
                            'language_id' => $languageId,
                            'name' => $translation
                        ]
                    );
                }
            }
        });
    }

    public function getEvents(): array {
        return [
            [
                'symbol' => 'order_completed',
                'translations' => [
                    'pl' => 'Oznaczenie zlecenia jako ukończone',
                    'en' => 'Order marked as completed',
                ]
            ],
            [
                'symbol' => 'incoming_order_deadline',
                'translations' => [
                    'pl' => 'Zbliżający się termin realizacji zlecenia',
                    'en' => 'Upcoming order deadline',
                ]
            ],
            [
                'symbol' => 'order_request_created',
                'translations' => [
                    'pl' => 'Otrzymanie nowego zapytania usługowego',
                    'en' => 'Receiving a new order request',
                ]
            ],
        ];
    }
}
