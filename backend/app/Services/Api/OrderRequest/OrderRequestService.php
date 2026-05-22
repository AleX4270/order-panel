<?php
declare(strict_types=1);

namespace App\Services\Api\OrderRequest;

use App\Dtos\Api\Address\AddressResolveDto;
use App\Dtos\Api\City\CityResolveDto;
use App\Dtos\Api\Client\ClientDto;
use App\Dtos\Api\OrderRequest\OrderRequestDto;
use App\Dtos\Api\OrderRequest\OrderRequestFilterDto;
use App\Enums\OrderStatusType;
use App\Enums\PriorityType;
use App\Models\Language;
use App\Models\Order;
use App\Models\OrderRequest;
use App\Models\OrderStatus;
use App\Models\OrderTranslation;
use App\Models\Priority;
use App\Repositories\OrderRequestRepository;
use App\Services\Api\Address\AddressService;
use App\Services\Api\City\CityService;
use App\Services\Api\Client\ClientService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderRequestService {
    public function __construct(
        private readonly AddressService $addressService,
        private readonly ClientService $clientService,
        private readonly CityService $cityService,
        private readonly OrderRequestRepository $orderRequestRepository,
    ) {}

    public function index(OrderRequestFilterDto $dto): Collection {
        $query = $this->orderRequestRepository->getAll($dto);

        $totalItems = $query->count();
        if(!empty($dto->page) && !empty($dto->pageSize)) {
            $items = $query->forPage($dto->page, $dto->pageSize)->get();
        }
        else {
            $items = $query->get();
        }

        return collect([
            'items' => $items ?? [],
            'count' => $totalItems,
        ]);
    }

    public function store(OrderRequestDto $dto): void {
        DB::beginTransaction();
        try {
            $city = $this->cityService->findOrCreate(new CityResolveDto(
                cityId: 0,
                cityName: $dto->city,
                provinceId: $dto->provinceId,
            ));

            $address = $this->addressService->findOrCreate(AddressResolveDto::fromArray([
                'address' => $dto->address,
                'postalCode' => $dto->postalCode,
                'cityId' => $city->id,
                'provinceId' => $dto->provinceId,
            ]));

            $client = $this->clientService->store(ClientDto::fromArray([
                'firstName' => $dto->firstName,
                'lastName' => $dto->lastName,
                'alias' => $dto->alias,
                'email' => $dto->email,
                'phoneNumber' => $dto->phoneNumber,
            ]));

            $orderRequest = OrderRequest::create([
                'client_id' => $client->id,
                'address_id' => $address->id,
                'remarks' => $dto->remarks,
                'ip_address' => $dto->ipAddress,
                'user_agent' => $dto->userAgent,
                'consent_given_at' => now(),
            ]);

            // TODO: Broadcast a notification

            DB::commit();
        }
        catch(Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function castToOrder(int $id): int {
        DB::beginTransaction();
        try {
            $orderRequest = OrderRequest::findOrFail($id);

            $order = Order::create([
                'symbol' => Str::random(16),
                'date_deadline' => Carbon::now()->addDays(14),
                'user_creation_id' => Auth::id(),
                'user_modification_id' => Auth::id(),
                'priority_id' => Priority::where('symbol', PriorityType::STANDARD->value)->firstOrFail()->id,
                'client_id' => $orderRequest->client_id,
                'status_id' => OrderStatus::where('symbol', OrderStatusType::IN_PROGRESS->value)->firstOrFail()->id,
                'created_at' => Carbon::now(),
                'address_id' => $orderRequest->address_id,
            ]);

            OrderTranslation::create([
                'order_id' => $order->id,
                'language_id' => Language::where('symbol', app()->getLocale())->firstOrFail()->id,
                'remarks' => $orderRequest->remarks,
            ]);

            $this->delete($id);

            DB::commit();
            return $order->id;
        }
        catch(Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete(int $id): void {
        OrderRequest::where('id', $id)->delete();
    }
}
