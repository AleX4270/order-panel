<?php
declare(strict_types=1);

namespace App\Http\Resources\Api\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Date;

class OrderResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'address' => $this->address,
            'postalCode' => $this->postalCode,
            'cityId' => $this->cityId,
            'cityName' => $this->cityName,
            'provinceId' => $this->provinceId,
            'provinceName' => $this->provinceName,
            'countryId' => $this->countryId,
            'priorityId' => $this->priorityId,
            'prioritySymbol' => $this->prioritySymbol,
            'priorityName' => $this->priorityName,
            'statusId' => $this->statusId,
            'statusName' => $this->statusName,
            'statusSymbol' => $this->statusSymbol,
            'dateCreated' => Date::parse($this->dateCreated)->format('Y-m-d'),
            'dateDeadline' => Date::parse($this->dateDeadline)->format('Y-m-d'),
            'dateCompleted' => !empty($this->dateCompleted) ? Date::parse($this->dateCompleted)->format('Y-m-d') : null,
            'phoneNumber' => $this->phoneNumber,
            'remarks' => $this->remarks,
            'isOverdue' => Date::parse($this->dateDeadline)->isPast(),
            'coordinates' => json_decode($this->coordinates, true),
        ];
    }
}
