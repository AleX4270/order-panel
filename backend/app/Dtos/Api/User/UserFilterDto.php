<?php

namespace App\Dtos\Api\User;

use App\Enums\SortDir;
use Carbon\Carbon;
use Illuminate\Support\Facades\Date;

final readonly class UserFilterDto {
    public function __construct(
        public ?int $page = null,
        public ?int $pageSize = null,
        public ?string $sortColumn = null,
        public ?SortDir $sortDir = null,
        public ?string $allFields = null,
        public ?Carbon $dateCreated = null,
        public ?Carbon $dateUpdated = null,
    ) {}

    public static function fromArray(array $data): self {
        return new UserFilterDto(
            page: $data['page'] ?? null,
            pageSize: $data['pageSize'] ?? null,
            sortColumn: $data['sortColumn'] ?? null,
            sortDir: !empty($data['sortDir']) ? SortDir::tryFrom($data['sortDir']) : null,
            allFields: $data['allFields'] ?? null,
            dateCreated: !empty($data['dateCreated']) ? Date::parse($data['dateCreated']) : null,
            dateUpdated: !empty($data['dateUpdated']) ? Date::parse($data['dateUpdated']) : null,
        );
    }
}