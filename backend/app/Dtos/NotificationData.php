<?php
declare(strict_types=1);

final readonly class NotificationData {
    public function __construct(
        public string $id,
        public string $title,
        public string $message,
    ) {}

    public function toArray(): array {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'message' => $this->message,
        ];
    }
}