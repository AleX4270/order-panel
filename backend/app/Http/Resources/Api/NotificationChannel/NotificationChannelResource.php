<?php
declare(strict_types=1);

namespace App\Http\Resources\Api\NotificationChannel;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationChannelResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'symbol' => $this->symbol,
            'name' => $this->translations->first()?->name,
        ];
    }
}
