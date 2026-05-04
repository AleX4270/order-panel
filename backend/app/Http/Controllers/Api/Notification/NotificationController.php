<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\Notification;

use App\Enums\HttpStatus;
use App\Http\Requests\Api\Notification\NotificationFilterRequest;
use App\Http\Resources\Api\Notification\NotificationResource;
use App\Http\Responses\Api\ApiResponse;
use App\Services\Api\Notification\NotificationService;
use Illuminate\Http\Request;

class NotificationController {
    public function __construct(
        private readonly NotificationService $notificationService,
    ) {}

    public function index(NotificationFilterRequest $request): ApiResponse {
        $result = $this->notificationService->index($request->toDto());

        return new ApiResponse(
            data: NotificationResource::collection($result),
            status: HttpStatus::OK,
            message: __('response.success'),
        );
    }

    public function markAsRead(Request $request): ApiResponse {
        $id = $request->post('id');

        if(empty($id)) {
            return new ApiResponse(
                status: HttpStatus::BAD_GATEWAY,
                message: __('response.badRequest'),
            );    
        }

        $this->notificationService->markAsRead($id);

        return new ApiResponse(
            status: HttpStatus::NO_CONTENT,
            message: __('response.success'),
        );
    }
}
