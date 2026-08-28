<?php
declare(strict_types=1);

namespace App\Http\Resources\Api\Status;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property string $symbol
 * @property string $name
 */
class StatusResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'symbol' => $this->symbol,
            'name' => $this->name,
        ];
    }
}
