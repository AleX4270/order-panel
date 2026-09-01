<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\Notification;

use App\Enums\HttpStatus;
use App\Http\Requests\Api\Notification\NotificationFilterRequest;
use App\Http\Requests\Api\Notification\NotificationMarkAsReadRequest;
use App\Http\Resources\Api\Notification\NotificationResource;
use App\Http\Responses\Api\ApiResponse;
use App\Services\Api\Notification\NotificationService;

class NotificationController {
    public function __construct(
        private readonly NotificationService $notificationService,
    ) {}

    public function index(NotificationFilterRequest $request): ApiResponse {
        $result = $this->notificationService->index($request->toDto());

        //TODO: Why doesn't it return count?
        return new ApiResponse(
            data: NotificationResource::collection($result),
            status: HttpStatus::OK,
            message: __('response.success'),
        );
    }

    public function markAsRead(NotificationMarkAsReadRequest $request): ApiResponse {
        $this->notificationService->markAsRead($request->validated('id'));

        return new ApiResponse(
            status: HttpStatus::OK,
            message: __('response.success'),
        );
    }
}
