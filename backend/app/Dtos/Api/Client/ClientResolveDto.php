<?php

namespace App\Dtos\Api\Client;

final readonly class ClientResolveDto {
    public function __construct(
        public string $phoneNumber,
    ) {}

    public static function fromArray(array $data): self {
        return new ClientResolveDto(
            phoneNumber: $data['phoneNumber'],
        );
    }
}