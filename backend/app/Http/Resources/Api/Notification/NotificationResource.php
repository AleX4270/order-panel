<?php
declare(strict_types=1);

namespace App\Http\Resources\Api\Notification;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Notifications\DatabaseNotification;

/**
 * @mixin DatabaseNotification
 */
class NotificationResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'title' => $this->data['title'],
            'message' => $this->data['message'],
            'readAt' => $this->read_at,
            'createdAt' => $this->data['createdAt'],
            'type' => $this->type,
        ];
    }
}
