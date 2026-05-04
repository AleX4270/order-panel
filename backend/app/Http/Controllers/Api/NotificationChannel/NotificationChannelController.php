<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\NotificationChannel;

use App\Enums\HttpStatus;
use App\Http\Requests\Api\NotificationChannel\NotificationChannelFilterRequest;
use App\Http\Resources\Api\NotificationChannel\NotificationChannelResource;
use App\Http\Responses\Api\ApiResponse;
use App\Services\Api\NotificationChannel\NotificationChannelService;

class NotificationChannelController {
    public function __construct(
        private readonly NotificationChannelService $notificationChannelService,
    ) {}

    public function index(NotificationChannelFilterRequest $request): ApiResponse {
        $result = $this->notificationChannelService->index($request->toDto());

        return new ApiResponse(
            data: NotificationChannelResource::collection($result),
            status: HttpStatus::OK,
            message: __('response.success'),
        );
    }
}
