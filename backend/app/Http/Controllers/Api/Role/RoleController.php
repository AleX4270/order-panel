<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\Role;

use App\Enums\HttpStatus;
use App\Http\Requests\Api\Role\RoleFilterRequest;
use App\Http\Responses\Api\ApiResponse;
use App\Services\Api\Role\RoleService;

class RoleController {
    public function __construct(
        private readonly RoleService $roleService,
    ) {}

    public function index(RoleFilterRequest $request): ApiResponse {
        $data = $this->roleService->index($request->toDto());

        return new ApiResponse(
            data: $data,
            status: HttpStatus::OK,
            message: __('response.success'),
        );
    }
}
