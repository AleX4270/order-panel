<?php
declare(strict_types=1);

namespace App\Http\Resources\Api\User;

use App\Models\Role;
use App\Models\User;
use App\Models\UserNotificationSetting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * @mixin User
 *
 * @property string $firstName
 * @property string $lastName
 * @property string $dateCreated
 * @property string $dateUpdated
 * @property bool $isInternal
 */
class UserShowResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'email' => $this->email,
            'dateCreated' => $this->dateCreated,
            'dateUpdated' => $this->dateUpdated,
            'roles' => $this->roles->map(fn(Role $role) => [
                'id' => $role->id,
                'symbol' => $role->name,
                'name' => $role->translation?->name,
            ]),
            'isInternal' => $this->isInternal,
            'notificationSettings' => $this->notificationSettings
                ->groupBy('notification_event_id')
                ->map(fn(Collection $settings, int|string $eventId) => [
                    'eventId' => (int)$eventId,
                    'channelIds' => $settings->map(
                        fn(UserNotificationSetting $setting) => (int)$setting->notification_channel_id
                    ),
                ])
                ->values(),
        ];
    }
}
