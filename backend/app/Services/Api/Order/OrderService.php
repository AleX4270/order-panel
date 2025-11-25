<?php
declare(strict_types=1);

namespace App\Services\Api\Order;

use App\Dtos\Api\Address\AddressResolveDto;
use App\Dtos\Api\Client\ClientResolveDto;
use App\Dtos\Api\Order\OrderDto;
use App\Dtos\Api\Order\OrderFilterDto;
use App\Models\Language;
use App\Models\Order;
use App\Models\OrderTranslation;
use App\Repositories\OrderRepository;
use App\Services\Api\Address\AddressService;
use App\Services\Api\Client\ClientService;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderService {
    public function __construct(
        private readonly AddressService $addressService,
        private readonly ClientService $clientService,
        private readonly OrderRepository $orderRepository,
    ) {}

    public function index(OrderFilterDto $dto): Collection {
        $query = $this->orderRepository->getAll($dto);

        $totalItems = $query->count();
        if(!empty($dto->page) && !empty($dto->pageSize)) {
            $items = $query->forPage($dto->page, $dto->pageSize)->get();
        }
        else {
            $items = $query->get();
        }

        $items = $items->map(function($item) {
            $item->isOverdue = Date::parse($item->date_deadline)->isPast();
            return $item;
        });

        return collect([
            'items' => $items->map->toCamelCaseKeys() ?? [],
            'count' => $totalItems,
        ]);
    }

    public function store(OrderDto $dto): Collection {
        DB::beginTransaction();
        try {
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
                'status_id' => $dto->statusId,
                'created_at' => $dto->dateCreation,
            ]);

            OrderTranslation::create([
                'order_id' => $order->id,
                'language_id' => Language::where('symbol', app()->getLocale())->value('id'),
                'remarks' => $dto->remarks
            ]);

            logger([$order]);

            DB::commit();
            return collect([
                'id' => $order->id,
            ]);
        }
        catch(Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            throw $e;
        }
    }

    public function update(OrderDto $dto): bool {
        //Sprawdź czy adres się zmienił, stwórz nowy jeśli tak
        //Sprawdź czy klient się zmienił, stwórz nowego jeśli tak
        //Zaktualizuj dane zlecenia

        return true;
    }
}
