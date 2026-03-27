<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\NotificationEvent;

use App\Enums\HttpStatus;
use App\Http\Requests\Api\NotificationEvent\NotificationEventFilterRequest;
use App\Http\Responses\Api\ApiResponse;
use App\Services\Api\NotificationEvent\NotificationEventService;

class NotificationEventController {
    public function __construct(
        private readonly NotificationEventService $notificationEventService,
    ) {}

    public function list(NotificationEventFilterRequest $request): ApiResponse {
        //
    }
}
