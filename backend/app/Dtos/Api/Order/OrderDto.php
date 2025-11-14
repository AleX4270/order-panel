<?php
declare(strict_types=1);

namespace App\Dtos\Api\Order;

use Carbon\Carbon;
use Illuminate\Support\Facades\Date;

final readonly class OrderDto {
    public function __construct(
        public ?int $id = null,
        public int $countryId,
        public int $provinceId,
        public int $cityId,
        public ?string $postalCode = null,
        public string $address,
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
            countryId: $data['countryId'],
            provinceId: $data['provinceId'],
            cityId: $data['cityId'],
            postalCode: $data['postalCode'] ?? null,
            address: $data['address'],
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