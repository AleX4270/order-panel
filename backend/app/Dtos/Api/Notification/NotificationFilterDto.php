<?php

namespace App\Dtos\Api\Notification;

final readonly class NotificationFilterDto {
    public function __construct(
        public int $userId,
    ) {}

    public static function fromArray(array $data): self {
        return new NotificationFilterDto(
            userId: $data['userId'] ?? null,
        );
    }
}