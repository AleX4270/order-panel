<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\User;

use App\Enums\HttpStatus;
use App\Http\Requests\Api\User\UserFilterRequest;
use App\Http\Requests\Api\User\UserRequest;
use App\Http\Responses\Api\ApiResponse;
use App\Services\Api\User\UserService;

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

    // public function show(Request $request): ApiResponse {
    //     $orderId = (int)$request->route('id');

    //     if(empty($orderId)) {
    //         return new ApiResponse(
    //             status: HttpStatus::BAD_REQUEST,
    //             message: __('response.badRequest'),
    //         );    
    //     }

    //     $result = $this->orderService->show($orderId);

    //     return new ApiResponse(
    //         data: $result,
    //         status: HttpStatus::OK,
    //         message: __('response.created'),
    //     );
    // }

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

    // public function delete(Request $request) {
    //     $orderId = (int)$request->route('id');

    //     if(empty($orderId)) {
    //         return new ApiResponse(
    //             status: HttpStatus::BAD_REQUEST,
    //             message: __('response.badRequest'),
    //         );    
    //     }

    //     $result = $this->orderService->delete($orderId);

    //     return new ApiResponse(
    //         status: HttpStatus::OK,
    //         message: __('response.deleted'),
    //     );
    // }
}
