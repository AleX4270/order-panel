<?php

namespace App\Dtos\Api\City;

use App\Enums\SortDir;

final readonly class CityFilterDto {
    public function __construct(
        public ?int $page = null,
        public ?int $pageSize = null,
        public ?string $sortColumn = null,
        public ?string $sortDir = null,
        public ?int $provinceId = null,
        public ?string $term = null,
    ) {}

    public static function fromArray(array $data): self {
        return new CityFilterDto(
            page: $data['page'] ?? null,
            pageSize: $data['pageSize'] ?? null,
            sortColumn: $data['sortColumn'] ?? null,
            sortDir: !empty($data['sortDir']) ? SortDir::tryFrom($data['sortDir']) : null,
            provinceId: $data['provinceId'] ?? null,
            term: $data['term'] ?? null,
        );
    }
}