<?php
declare(strict_types=1);

namespace App\Services\Api\Order;

use App\Dtos\Api\Address\AddressDto;
use App\Dtos\Api\Address\AddressResolveDto;
use App\Dtos\Api\Client\ClientResolveDto;
use App\Dtos\Api\Order\OrderDto;
use App\Dtos\Api\Order\OrderFilterDto;
use App\Models\Language;
use App\Models\Order;
use App\Models\OrderTranslation;
use App\Services\Api\Address\AddressService;
use App\Services\Api\Client\ClientService;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderService {
    public function __construct(
        private readonly AddressService $addressService,
        private readonly ClientService $clientService,
    ) {}

    public function index(OrderFilterDto $dto): Collection {
        $query = Order::query()
            ->from('orders as o')
            ->select([
                'o.id',
                'a.address',
                'a.postal_code',
                'c.name as cityName',
                'p.name as provinceName',
                'pr.symbol as prioritySymbol',
                'prt.name as priorityName',
                'os.symbol as statusSymbol',
                'o.created_at as dateCreated',
                'o.date_deadline',
                'ot.remarks',
            ])
            ->join('clients as cl', 'cl.id', '=', 'o.client_id')
            ->join('addresses as a', 'a.id', '=', 'cl.address_id')
            ->join('cities as c', 'c.id', '=', 'a.city_id')
            ->join('provinces as p', 'p.id', '=', 'c.province_id')
            ->join('priorities as pr', 'pr.id', '=', 'o.priority_id')
            ->join('priority_translations as prt', 'prt.priority_id', '=', 'pr.id')
            ->join('languages as prl', function($prlJoin) {
                $prlJoin->on('prl.id', '=', 'prt.language_id')
                    ->where('prl.symbol', app()->getLocale());
            })
            ->join('order_statuses as os', 'os.id', '=', 'o.status_id')
            ->join('order_translations as ot', 'ot.order_id', '=', 'o.id')
            ->join('languages as otl', function($prlJoin) {
                $prlJoin->on('otl.id', '=', 'ot.language_id')
                    ->where('otl.symbol', app()->getLocale());
            });

        return $query->get()->map->toCamelCaseKeys();
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
