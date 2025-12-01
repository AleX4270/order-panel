<?php
declare(strict_types=1);

namespace App\Services\Api\User;

use App\Dtos\Api\User\UserFilterDto;
use App\Repositories\UserRepository;
use Illuminate\Support\Collection;

class UserService {
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {}

    public function index(UserFilterDto $dto): Collection {
        $query = $this->userRepository->getAll($dto);

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

    // public function show(int $orderId): Order {
    //     $data = $this->orderRepository->getOne($orderId)->first();

    //     $data->dateCreated = Date::parse($data->dateCreated)->format('Y-m-d');
    //     $data->dateDeadline = Date::parse($data->dateDeadline)->format('Y-m-d');
    //     $data->dateCompleted = !empty($data->dateCompleted) ? Date::parse($data->dateCompleted)->format('Y-m-d') : null;

    //     return $data;
    // }

    // public function store(OrderDto $dto): Collection {
    //     DB::beginTransaction();
    //     try {
    //         $address = $this->addressService->findOrCreate(AddressResolveDto::fromArray([
    //             'address' => $dto->addressDto->address,
    //             'postalCode' => $dto->addressDto->postalCode,
    //             'cityId' => $dto->addressDto->cityId,
    //             'cityName' => $dto->addressDto->cityName,
    //             'provinceId' => $dto->addressDto->provinceId,
    //         ]));

    //         $client = $this->clientService->findOrCreate(ClientResolveDto::fromArray([
    //             'address' => $address,
    //             'phoneNumber' => $dto->phoneNumber,
    //         ]));

    //         $order = Order::create([
    //             'symbol' => Str::random(16),
    //             'date_deadline' => $dto->dateDeadline,
    //             'user_creation_id' => Auth::id(),
    //             'user_modification_id' => Auth::id(),
    //             'priority_id' => $dto->priorityId,
    //             'client_id' => $client->id,
    //             'status_id' => $dto->statusId,
    //             'created_at' => $dto->dateCreation,
    //         ]);

    //         OrderTranslation::create([
    //             'order_id' => $order->id,
    //             'language_id' => Language::where('symbol', app()->getLocale())->value('id'),
    //             'remarks' => $dto->remarks
    //         ]);

    //         DB::commit();
    //         return collect([
    //             'id' => $order->id,
    //         ]);
    //     }
    //     catch(Exception $e) {
    //         Log::error($e);
    //         DB::rollBack();
    //         throw $e;
    //     }
    // }

    // public function update(OrderDto $dto): bool {
    //     DB::beginTransaction();
    //     try {
    //         $address = $this->addressService->findOrCreate(AddressResolveDto::fromArray([
    //             'address' => $dto->addressDto->address,
    //             'postalCode' => $dto->addressDto->postalCode,
    //             'cityId' => $dto->addressDto->cityId,
    //             'cityName' => $dto->addressDto->cityName,
    //             'provinceId' => $dto->addressDto->provinceId,
    //         ]));

    //         $client = $this->clientService->findOrCreate(ClientResolveDto::fromArray([
    //             'address' => $address,
    //             'phoneNumber' => $dto->phoneNumber,
    //         ]));

    //         Order::where('id', $dto->id)
    //             ->update([
    //                 'date_deadline' => $dto->dateDeadline,
    //                 'user_modification_id' => Auth::id(),
    //                 'priority_id' => $dto->priorityId,
    //                 'client_id' => $client->id,
    //                 'status_id' => $dto->statusId,
    //                 'created_at' => $dto->dateCreation,
    //                 'date_completed' => $dto->dateCompleted,
    //             ]);

    //         OrderTranslation::where('order_id', $dto->id)
    //             ->update([
    //                 'remarks' => $dto->remarks,
    //             ]);

    //         DB::commit();
    //         return true;
    //     }
    //     catch(Exception $e) {
    //         Log::error($e);
    //         DB::rollBack();
    //         throw $e;
    //     }
    // }

    // public function delete(int $orderId): void {
    //     logger($orderId);
    //     Order::where('id', $orderId)->update([
    //         'is_active' => 0,
    //     ]);
    // }
}
