<?php

namespace App\Dtos\Api\Order;

use App\Enums\SortDir;
use Carbon\Carbon;
use Illuminate\Support\Facades\Date;

final readonly class OrderFilterDto {
    public function __construct(
        public ?int $page = null,
        public ?int $pageSize = null,
        public ?string $sortColumn = null,
        public ?SortDir $sortDir = null,
        public ?string $allFields = null,
        public ?array $priorityIds = null,
        public ?array $statusIds = null,
        public ?Carbon $dateCreation = null,
        public ?Carbon $dateDeadline = null,
    ) {}

    public static function fromArray(array $data): self {
        return new OrderFilterDto(
            page: $data['page'] ?? null,
            pageSize: $data['pageSize'] ?? null,
            sortColumn: $data['sortColumn'] ?? null,
            sortDir: !empty($data['sortDir']) ? SortDir::tryFrom($data['sortDir']) : null,
            allFields: $data['allFields'] ?? null,
            priorityIds: $data['priorityIds'] ?? null,
            statusIds: $data['statusIds'] ?? null,
            dateCreation: !empty($data['dateCreation']) ? Date::parse($data['dateCreation']) : null,
            dateDeadline: !empty($data['dateDeadline']) ? Date::parse($data['dateDeadline']) : null,
        );
    }
}