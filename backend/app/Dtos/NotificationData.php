<?php
declare(strict_types=1);

namespace App\Dtos;

final readonly class NotificationData {
    public function __construct(
        public string $title,
        public string $message,
        public string $createdAt,
        public ?string $readAt = null,
    ) {}

    public function toArray(): array {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'readAt' => $this->readAt,
            'createdAt' => $this->createdAt,
        ];
    }
}