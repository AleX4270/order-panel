<?php

namespace App\Dtos\Api\Client;

use App\Models\Address;

final readonly class ClientResolveDto {
    public function __construct(
        public Address $address,
        public string $phoneNumber,
    ) {}

    public static function fromArray(array $data): self {
        return new ClientResolveDto(
            address: $data['address'],
            phoneNumber: $data['phoneNumber'],
        );
    }
}