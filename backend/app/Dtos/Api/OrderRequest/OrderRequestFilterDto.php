<?php

namespace App\Dtos\Api\OrderRequest;

use App\Enums\SortDir;

final readonly class OrderRequestFilterDto {
    public function __construct(
        public ?int $page = null,
        public ?int $pageSize = null,
        public ?string $sortColumn = null,
        public ?SortDir $sortDir = null,
        public ?int $distanceFromHeadquarters = null,
        public ?string $dateCreation = null,
        public ?string $allFields = null,
    ) {}

    public static function fromArray(array $data): self {
        return new OrderRequestFilterDto(
            page: $data['page'] ?? null,
            pageSize: $data['pageSize'] ?? null,
            sortColumn: $data['sortColumn'] ?? null,
            sortDir: !empty($data['sortDir']) ? SortDir::tryFrom($data['sortDir']) : null,
            distanceFromHeadquarters: $data['distanceFromHeadquarters'] ?? null,
            dateCreation: $data['dateCreation'] ?? null,
            allFields: $data['allFields'] ?? null,
        );
    }
}
