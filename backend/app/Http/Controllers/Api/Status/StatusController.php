<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\Status;

use App\Enums\HttpStatus;
use App\Http\Requests\Api\Status\StatusFilterRequest;
use App\Http\Resources\Api\Status\StatusResource;
use App\Http\Responses\Api\ApiResponse;
use App\Services\Api\Status\StatusService;

class StatusController {
    public function __construct(
        private readonly StatusService $statusService,
    ) {}

    public function index(StatusFilterRequest $request): ApiResponse {
        $data = $this->statusService->index($request->toDto());

        return new ApiResponse(
            data: [
                'items' => StatusResource::collection($data['items']),
                'count' => $data['count'],
            ],
            status: HttpStatus::OK,
            message: __('response.success'),
        );
    }
}
