<?php
declare(strict_types=1);

namespace App\Services\Api\Order;

use App\Dtos\Api\Address\AddressDto;
use App\Dtos\Api\Order\OrderDto;
use App\Services\Api\Address\AddressService;
use Illuminate\Support\Collection;

class OrderService {
    public function __construct(
        private readonly AddressService $addressService,
    ) {}

    public function index(): Collection {
        return collect([]);
    }

    public function store(OrderDto $dto): bool {
        //Wybierz lub zwróć nowy id adresu
            //a. Jeśli miasto podano jako tag to mozna od razu przejsc do tworzenia adresu
        $address = $this->addressService->findOrCreate($dto->addressDto);
        
        //Wybierz lub zwróć nowy id klienta
        //Zapisz dane zlecenia
    }

    public function update(OrderDto $dto): bool {
        //Sprawdź czy adres się zmienił, stwórz nowy jeśli tak
        //Sprawdź czy klient się zmienił, stwórz nowego jeśli tak
        //Zaktualizuj dane zlecenia
    }
}
