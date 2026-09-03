<?php
declare(strict_types=1);

namespace App\Http\Resources\Api\Role;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Role
 */
class RoleResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'symbol' => $this->name,
            'name' => $this->translations->first()?->name,
        ];
    }
}
