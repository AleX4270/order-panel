<?php
declare(strict_types=1);

namespace App\Dtos\Api\OrderRequest;

final readonly class OrderRequestDto {
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $email,
        public string $phoneNumber,
        public int $countryId,
        public int $provinceId,
        public string $city,
        public string $postalCode,
        public string $address,
        public string $ipAddress,
        public string $userAgent,
        public ?string $alias = null,
        public ?string $remarks = null,
    ) {}

    public static function fromArray(array $data): self {
        return new OrderRequestDto(
            firstName: $data['firstName'],
            lastName: $data['lastName'],
            email: $data['email'],
            phoneNumber: $data['phoneNumber'],
            countryId: $data['countryId'],
            provinceId: $data['provinceId'],
            city: $data['city'],
            postalCode: $data['postalCode'],
            address: $data['address'],
            ipAddress: $data['ipAddress'],
            userAgent: $data['userAgent'],
            alias: $data['alias'] ?? null,
            remarks: $data['remarks'] ?? null,
        );
    }
}
