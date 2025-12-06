<?php
declare(strict_types=1);

namespace App\Dtos\Api\User;

final readonly class UserDto {
    public function __construct(
        public ?int $id = null,
        public ?string $firstName = null,
        public ?string $lastName = null,
        public string $username,
        public string $email,
        public ?string $password = null,
        public ?string $passwordConfirmed = null,
        public ?array $roles = null,
    ) {}

    public static function fromArray(array $data): self {
        return new UserDto(
            id: $data['id'] ?? null,
            firstName: $data['firstName'] ?? null,
            lastName: $data['lastName'] ?? null,
            username: $data['username'],
            email: $data['email'],
            password: $data['password'] ?? null,
            passwordConfirmed: $data['passwordConfirmed'] ?? null,
            roles: $data['roles'] ?? null,
        );
    }

    public function isNewUser(): bool {
        return empty($this->id);
    }
}