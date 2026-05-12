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
            'first_name' => $dto->firstName,
            'last_name' => $dto->lastName,
            'email' => $dto->email,
            'phone_number' => $dto->phoneNumber,
            'is_blocked' => $dto->isBlocked,
            'is_active' => $dto->isActive,
        ]);

        return $result;
    }

    // TODO: Think of a solution to distinguish new users from existing ones
    public function findOrCreate(ClientResolveDto $dto): Client {
        $client = Client::where('phone_number', $dto->phoneNumber)->first();

        if(!empty($client)) {
            return $client;
        }

        return $this->store(ClientDto::fromArray([
            'phoneNumber' => $dto->phoneNumber,
        ]));
    }
}
