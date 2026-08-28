<?php
declare(strict_types=1);

namespace App\Http\Resources\Api\Priority;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property string $symbol
 * @property bool $is_active
 * @property string $name
 */
class PriorityResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'symbol' => $this->symbol,
            'isActive' => $this->is_active,
            'name' => $this->name,
        ];
    }
}
