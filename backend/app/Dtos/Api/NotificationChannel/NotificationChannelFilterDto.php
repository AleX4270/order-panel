<?php

namespace App\Dtos\Api\NotificationChannel;

use App\Enums\SortDir;

final readonly class NotificationChannelFilterDto {
    public function __construct(
        public ?string $sortColumn = null,
        public ?SortDir $sortDir = null,
    ) {}

    public static function fromArray(array $data): self {
        return new NotificationChannelFilterDto(
            sortColumn: $data['sortColumn'] ?? null,
            sortDir: !empty($data['sortDir']) ? SortDir::tryFrom($data['sortDir']) : null,
        );
    }
}
