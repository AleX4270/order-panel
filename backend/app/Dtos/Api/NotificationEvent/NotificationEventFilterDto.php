<?php

namespace App\Dtos\Api\NotificationEvent;

use App\Enums\SortDir;

final readonly class NotificationEventFilterDto {
    public function __construct(
        public ?string $sortColumn = null,
        public ?SortDir $sortDir = null,
    ) {}

    public static function fromArray(array $data): self {
        return new NotificationEventFilterDto(
            sortColumn: $data['sortColumn'] ?? null,
            sortDir: !empty($data['sortDir']) ? SortDir::tryFrom($data['sortDir']) : null,
        );
    }
}
