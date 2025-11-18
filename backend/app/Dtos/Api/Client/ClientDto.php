<?php

namespace App\Dtos\Api\Client;

final readonly class ClientDto {
    //? This is optional on purpose. Clients work in a quite different way
    public function __construct(
        public ?int $id,
        public ?string $alias,
        public ?string $firstName,
        public ?string $lastName,
        public ?string $email,
        public ?string $phoneNumber,
        public ?int $addressId,
        public bool $isBlocked,
        public bool $isActive,
    ) {}

    public static function fromArray(array $data): self {
        return new ClientDto(
            id: $data['id'] ?? null,
            alias: $data['alias'] ?? null,
            firstName: $data['firstName'] ?? null,
            lastName: $data['lastName'] ?? null,
            email: $data['email'] ?? null,
            phoneNumber: $data['phoneNumber'] ?? null,
            addressId: $data['addressId'] ?? null,
            isBlocked: $data['isBlocked'] ?? false,
            isActive: $data['isActive'] ?? true,
        );
    }
}