<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Language;
use App\Models\NotificationChannel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationChannelSeeder extends Seeder {
    public function run(): void {
        DB::transaction(function() {
            $channels = $this->getChannels();
            foreach($channels as $channel) {
                $newChannel = NotificationChannel::updateOrCreate(
                    ['symbol' => $channel['symbol']],
                    ['symbol' => $channel['symbol']],
                );
    
                foreach($channel['translations'] as $language => $translation) {
                    $languageId = Language::where('symbol', $language)->first()?->id;
    
                    $newChannel->translations()->updateOrCreate(
                        [
                            'language_id' => $languageId,
                        ],
                        [
                            'notification_channel_id' => $newChannel->id,
                            'language_id' => $languageId,
                            'name' => $translation
                        ]
                    );
                }
            }
        });
    }

    public function getChannels(): array {
        return [
            [
                'symbol' => 'broadcast',
                'translations' => [
                    'pl' => 'Powiadomienia w aplikacji',
                    'en' => 'In-app notifications',
                ]
            ],
            [
                'symbol' => 'mail',
                'translations' => [
                    'pl' => 'Powiadomienia e-mail',
                    'en' => 'Email notifications',
                ]
            ],
        ];
    }
}
