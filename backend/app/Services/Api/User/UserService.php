<?php
declare(strict_types=1);

namespace App\Services\Api\User;

use App\Dtos\Api\User\UserDto;
use App\Dtos\Api\User\UserFilterDto;
use App\Exceptions\Api\User\InternalUserCannotBeDeletedException;
use App\Models\User;
use App\Models\UserNotificationSetting;
use App\Repositories\UserRepository;
use Exception;
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

        return collect([
            'items' => $items,
            'count' => $totalItems,
        ]);
    }

    public function show(int $userId): User {
        return $this->userRepository->getOne($userId)->firstOrFail();
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
            }
            else {
                $user = User::where('id', $dto->id)->first();
                $user->update($userData);
            }

            if(!$user->is_internal) {
                $user->syncRoles([]);

                if(!empty($dto->roles)) {
                    foreach($dto->roles as $role) {
                        $user->assignRole($role);
                    }
                }
            }

            UserNotificationSetting::where('user_id', $dto->id)->delete();
            foreach($dto->notificationSettings as $notificationSetting) {
                if(empty($notificationSetting['channelIds'])) {
                    continue;
                }

                foreach($notificationSetting['channelIds'] as $channelId) {
                    $setting = new UserNotificationSetting();
                    $setting->user_id = $user->id;
                    $setting->notification_event_id = $notificationSetting['eventId'];
                    $setting->notification_channel_id = $channelId;
                    $setting->save();
                }
            }

            DB::commit();
            return $user->id;
        }
        catch(Exception $e) {
            Log::error($e);
            DB::rollBack();
            throw $e;
        }
    }

    public function delete(int $userId): void {
        $user = User::findOrFail($userId);

        if($user->is_internal) {
            throw new InternalUserCannotBeDeletedException();
        }

        $user->is_active = false;
        $user->save();
    }
}
