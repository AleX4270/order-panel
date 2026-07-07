<?php
declare(strict_types=1);

namespace App\Http\Resources\Api\NotificationEvent;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\NotificationEvent;

/**
 * @mixin NotificationEvent
 */
class NotificationEventResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'symbol' => $this->symbol,
            'name' => $this->translations->first()?->name,
        ];
    }
}
