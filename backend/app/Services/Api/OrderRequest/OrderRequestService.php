<?php
declare(strict_types=1);

namespace App\Services\Api\OrderRequest;

use App\Dtos\Api\Address\AddressResolveDto;
use App\Dtos\Api\City\CityResolveDto;
use App\Dtos\Api\Client\ClientDto;
use App\Dtos\Api\OrderRequest\OrderRequestDto;
use App\Dtos\Api\OrderRequest\OrderRequestFilterDto;
use App\Models\OrderRequest;
use App\Repositories\OrderRequestRepository;
use App\Services\Api\Address\AddressService;
use App\Services\Api\City\CityService;
use App\Services\Api\Client\ClientService;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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
}
