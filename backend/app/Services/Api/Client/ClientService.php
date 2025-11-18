<?php
declare(strict_types=1);

namespace App\Services\Api\Client;

use App\Dtos\Api\Client\ClientDto;
use App\Dtos\Api\Client\ClientResolveDto;
use App\Models\Client;

class ClientService {
    public function store(ClientDto $dto): Client {
        $result = Client::create([ 
            'alias' => $dto->alias,
            'firstName' => $dto->firstName,
            'lastName' => $dto->lastName,
            'email' => $dto->email,
            'phoneNumber' => $dto->phoneNumber,
            'addressId' => $dto->addressId,
            'isBlocked' => $dto->isBlocked,
            'isActive' => $dto->isActive,
        ]);

        return $result;
    }

    public function findOrCreate(ClientResolveDto $dto): Client {
        $client = Client::where('address_id', $dto->address->id)
            ->where('phone_number', $dto->phoneNumber)
            ->first();

        if(!empty($client)) {
            return $client;
        }

        return $this->store(ClientDto::fromArray([
            'phoneNumber' => $dto->phoneNumber,
            'addressId' => $dto->address->id,
        ]));
    }
}
