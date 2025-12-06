<?php
declare(strict_types=1);

namespace App\Services\Api\User;

use App\Dtos\Api\User\UserDto;
use App\Dtos\Api\User\UserFilterDto;
use App\Models\User;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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

        $items = $items->map(function (User $user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'firstName' => $user->firstName,
                'lastName' => $user->lastName,
                'email' => $user->email,
                'dateCreated' => $user->dateCreated,
                'dateUpdated' => $user->dateUpdated,
                'roles' => $this->mapRoles($user->roles),
            ];
        });

        return collect([
            'items' => $items ?? [],
            'count' => $totalItems,
        ]);
    }

    public function show(int $orderId): array {
        $data = $this->userRepository->getOne($orderId)->first();
        
        $data = [
            'id' => $data->id,
            'name' => $data->name,
            'firstName' => $data->firstName,
            'lastName' => $data->lastName,
            'email' => $data->email,
            'dateCreated' => $data->dateCreated,
            'dateUpdated' => $data->dateUpdated,
            'roles' => $this->mapRoles($data->roles),
        ];

        return $data;
    }

    public function save(UserDto $dto): int {
        DB::beginTransaction();
        try {
            $userData = [
                'first_name' => $dto->firstName,
                'last_name' => $dto->lastName,
                'name' => $dto->username,
                'email' => $dto->email,
            ];

            if(!empty($dto->password)) {
                $userData['password'] = Hash::make($dto->password);
            }

            if($dto->isNewUser()) {
                $user = User::create($userData);
                $userId = $user->id;
            }
            else {
                $user = User::where('id', $dto->id)->first();
                $user->update($userData);
                $userId = $dto->id;
            }

            $user->syncRoles([]);

            if(!empty($dto->roles)) {
                foreach($dto->roles as $role) {
                    $user->assignRole($role);
                }
            }

            DB::commit();
            return $userId;
        }
        catch(Exception $e) {
            Log::error($e);
            DB::rollBack();
            throw $e;
        }
    }

    public function delete(int $userId): void {
        User::where('id', $userId)->update([
            'is_active' => 0,
        ]);
    }

    private function mapRoles(EloquentCollection $roles): Collection {
        return $roles->map(function($role) {
            return [
                'id'     => $role->id,
                'symbol' => $role->name,
                'name'   => $role->translation?->name ?? null,
            ];
        });
    }
}
