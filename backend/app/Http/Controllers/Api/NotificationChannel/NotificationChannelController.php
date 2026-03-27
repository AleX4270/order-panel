<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\NotificationChannel;

use App\Enums\HttpStatus;
use App\Http\Requests\Api\NotificationChannel\NotificationChannelFilterRequest;
use App\Http\Responses\Api\ApiResponse;
use App\Services\Api\NotificationChannel\NotificationChannelService;

class NotificationChannelController {
    public function __construct(
        private readonly NotificationChannelService $notificationChannelService,
    ) {}

    public function list(NotificationChannelFilterRequest $request): ApiResponse {
        //
    }
}
