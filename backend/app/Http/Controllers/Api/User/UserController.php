<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\User;

use App\Enums\HttpStatus;
use App\Http\Requests\Api\User\UserFilterRequest;
use App\Http\Requests\Api\User\UserRequest;
use App\Http\Responses\Api\ApiResponse;
use App\Models\User;
use App\Services\Api\User\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController {
    public function __construct(
        private readonly UserService $userService,
    ) {}

    public function index(UserFilterRequest $request): ApiResponse {
        $result = $this->userService->index($request->toDto());

        return new ApiResponse(
            data: $result,
            status: HttpStatus::OK,
            message: __('response.success'),
        );
    }

    public function show(Request $request): ApiResponse {
        Gate::authorize('show', User::class);

        $userId = (int)$request->route('id');

        if(empty($userId)) {
            return new ApiResponse(
                status: HttpStatus::BAD_REQUEST,
                message: __('response.badRequest'),
            );    
        }

        $result = $this->userService->show($userId);

        return new ApiResponse(
            data: $result,
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

    public function delete(Request $request) {
        $userId = (int)$request->route('id');
        if(empty($userId)) {
            return new ApiResponse(
                status: HttpStatus::BAD_REQUEST,
                message: __('response.badRequest'),
            );    
        }

        Gate::authorize('delete', User::findOrFail($userId));
        $this->userService->delete($userId);

        return new ApiResponse(
            status: HttpStatus::OK,
            message: __('response.deleted'),
        );
    }
}
