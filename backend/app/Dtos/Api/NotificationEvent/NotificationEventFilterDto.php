<?php

namespace App\Dtos\Api\NotificationEvent;

use App\Enums\SortDir;

final readonly class NotificationEventFilterDto {
    public function __construct(
        public ?int $page = null,
        public ?int $pageSize = null,
        public ?string $sortColumn = null,
        public ?string $sortDir = null,
    ) {}

    public static function fromArray(array $data): self {
        return new NotificationEventFilterDto(
            page: $data['page'] ?? null,
            pageSize: $data['pageSize'] ?? null,
            sortColumn: $data['sortColumn'] ?? null,
            sortDir: !empty($data['sortDir']) ? SortDir::tryFrom($data['sortDir']) : null,
        );
    }
}
