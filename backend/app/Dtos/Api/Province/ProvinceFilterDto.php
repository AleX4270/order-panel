<?php

namespace App\Dtos\Api\Province;

use App\Enums\SortDir;

final readonly class ProvinceFilterDto {
    public function __construct(
        public ?int $page = null,
        public ?int $pageSize = null,
        public ?string $sortColumn = null,
        public ?string $sortDir = null,
        public ?int $countryId = null,
    ) {}

    public static function fromArray(array $data): self {
        return new ProvinceFilterDto(
            page: $data['page'] ?? null,
            pageSize: $data['pageSize'] ?? null,
            sortColumn: $data['sortColumn'] ?? null,
            sortDir: !empty($data['sortDir']) ? SortDir::tryFrom($data['sortDir']) : null,
            countryId: $data['countryId'] ?? null,
        );
    }
}