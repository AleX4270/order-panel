<?php
declare(strict_types=1);

namespace App\Http\Resources\Api\Company;

use App\ValueObjects\Coordinates;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Company;

/**
 * @mixin Company
 */
class CompanyResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address->address,
            'postalCode' => $this->address->postal_code,
            'cityId' => $this->address->city_id,
            'provinceId' => $this->address->city->province_id,
            'countryId' => $this->address->city->province->country_id,
            'coordinates' => new Coordinates(
                $this->address->latitude,
                $this->address->longitude,
            ),
        ];
    }
}
