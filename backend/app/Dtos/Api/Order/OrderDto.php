<?php
declare(strict_types=1);

namespace App\Dtos\Api\Order;

use App\Dtos\Api\Address\AddressDto;
use Carbon\Carbon;
use Illuminate\Support\Facades\Date;

final readonly class OrderDto {
    public function __construct(
        public ?int $id = null,
        public AddressDto $addressDto,
        public string $phoneNumber,
        public int $priorityId,
        public int $statusId,
        public Carbon $dateCreation,
        public Carbon $dateDeadline,
        public ?Carbon $dateCompleted = null,
        public ?string $remarks = null,
    ) {}

    public static function fromArray(array $data): self {
        return new OrderDto(
            id: $data['id'] ?? null,
            addressDto: AddressDto::fromArray([
                'address' => $data['address'],
                'postalCode' => $data['postalCode'] ?? null,
                'cityId' => $data['cityId'],
                'cityName' => $data['cityName'] ?? null,
                'provinceId' => $data['provinceId'],
                'countryId' => $data['countryId'],
            ]),
            phoneNumber: $data['phoneNumber'],
            priorityId: $data['priorityId'],
            statusId: $data['statusId'],
            dateCreation: Date::parse($data['dateCreation']),
            dateDeadline: Date::parse($data['dateDeadline']),
            dateCompleted: !empty($data['dateCompleted']) ? Date::parse($data['dateCompleted']) : null,
            remarks: $data['remarks'] ?? null,
        );
    }

    public function isNewOrder(): bool {
        return empty($this->id);
    }
}