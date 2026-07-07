<?php
declare(strict_types=1);

namespace App\Http\Resources\Api\OrderRequest;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Date;

/**
 * @property int $id
 * @property string $firstName
 * @property string $lastName
 * @property string $email
 * @property string $phoneNumber
 * @property string $address
 * @property string $postalCode
 * @property string $cityName
 * @property string $provinceName
 * @property string $remarks
 * @property string $dateCreated
 * @property string $coordinates
 * @property float $distance
 */
class OrderRequestResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'email' => $this->email,
            'phoneNumber' => $this->phoneNumber,
            'address' => $this->address,
            'postalCode' => $this->postalCode,
            'cityName' => $this->cityName,
            'provinceName' => $this->provinceName,
            'remarks' => $this->remarks,
            'dateCreated' => Date::parse($this->dateCreated)->format('Y-m-d H:i:s'),
            'coordinates' => json_decode($this->coordinates, true),
            'distance' => number_format($this->distance / 1000, 2),
        ];
    }
}
