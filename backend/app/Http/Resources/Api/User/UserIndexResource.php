<?php
declare(strict_types=1);

namespace App\Http\Resources\Api\User;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 *
 * @property string $firstName
 * @property string $lastName
 * @property string $dateCreated
 * @property string $dateUpdated
 * @property bool $isInternal
 */
class UserIndexResource extends JsonResource {
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
        ];
    }
}
