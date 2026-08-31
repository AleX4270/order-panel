<?php

namespace App\Dtos\Api\Notification;

final readonly class NotificationFilterDto {
    public function __construct(
        public ?bool $onlyUnread = false,
    ) {}

    public static function fromArray(array $data): self {
        return new NotificationFilterDto(
            onlyUnread: $data['onlyUnread'] ?? null,
        );
    }
}
