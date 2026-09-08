<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\User;

use App\Enums\HttpStatus;
use App\Http\Requests\Api\User\UserDeleteRequest;
use App\Http\Requests\Api\User\UserFilterRequest;
use App\Http\Requests\Api\User\UserRequest;
use App\Http\Requests\Api\User\UserShowRequest;
use App\Http\Resources\Api\User\UserIndexResource;
use App\Http\Resources\Api\User\UserShowResource;
use App\Http\Responses\Api\ApiResponse;
use App\Services\Api\User\UserService;

class UserController {
    public function __construct(
        private readonly UserService $userService,
    ) {}

    public function index(UserFilterRequest $request): ApiResponse {
        $result = $this->userService->index($request->toDto());

        return new ApiResponse(
            data: [
                'items' => UserIndexResource::collection($result['items']),
                'count' => $result['count'],
            ],
            status: HttpStatus::OK,
            message: __('response.success'),
        );
    }

    public function show(UserShowRequest $request): ApiResponse {
        $user = $this->userService->show($request->getUserId());

        return new ApiResponse(
            data: new UserShowResource($user),
            status: HttpStatus::OK,
            message: __('response.success'),
        );
    }

    public function store(UserRequest $request): ApiResponse {
        $this->userService->save($request->toDto());

        return new ApiResponse(
            status: HttpStatus::CREATED,
            message: __('response.created'),
        );
    }

    public function update(UserRequest $request): ApiResponse {
        $this->userService->save($request->toDto());

        return new ApiResponse(
            status: HttpStatus::NO_CONTENT,
            message: __('response.success'),
        );
    }

    public function delete(UserDeleteRequest $request): ApiResponse {
        $userId = $request->getUserId();
        $this->userService->delete($userId);

        return new ApiResponse(
            status: HttpStatus::OK,
            message: __('response.deleted'),
        );
    }
}
