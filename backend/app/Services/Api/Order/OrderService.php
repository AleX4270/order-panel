<?php
declare(strict_types=1);

namespace App\Services\Api\Order;

use App\Dtos\Api\Address\AddressDto;
use App\Dtos\Api\Address\AddressResolveDto;
use App\Dtos\Api\Client\ClientResolveDto;
use App\Dtos\Api\Order\OrderDto;
use App\Models\Order;
use App\Services\Api\Address\AddressService;
use App\Services\Api\Client\ClientService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OrderService {
    public function __construct(
        private readonly AddressService $addressService,
        private readonly ClientService $clientService,
    ) {}

    public function index(): Collection {
        return collect([]);
    }

    public function store(OrderDto $dto): Collection {
        $address = $this->addressService->findOrCreate(AddressResolveDto::fromArray([
            'address' => $dto->addressDto->address,
            'postalCode' => $dto->addressDto->postalCode,
            'cityId' => $dto->addressDto->cityId,
            'cityName' => $dto->addressDto->cityName,
            'provinceId' => $dto->addressDto->provinceId,
        ]));
        $client = $this->clientService->findOrCreate(ClientResolveDto::fromArray([
            'address' => $address,
            'phoneNumber' => $dto->phoneNumber,
        ]));

        $order = Order::create([
            'symbol' => Str::random(16),
            'date_deadline' => $dto->dateDeadline,
            'user_creation_id' => Auth::id(),
            'user_modification_id' => Auth::id(),
            'priority_id' => $dto->priorityId,
            'client_id' => $client->id,
            'order_status_id' => $dto->statusId,
            'created_at' => $dto->dateCreation,
        ]);

        return collect([
            'id' => $order->id,
        ]);
    }

    // public function update(OrderDto $dto): bool {
        //Sprawdź czy adres się zmienił, stwórz nowy jeśli tak
        //Sprawdź czy klient się zmienił, stwórz nowego jeśli tak
        //Zaktualizuj dane zlecenia
    // }
}
