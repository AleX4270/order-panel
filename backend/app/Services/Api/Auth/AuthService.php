<?php
declare(strict_types=1);

namespace App\Services\Api;

use Illuminate\Support\Facades\Auth;

class AuthService {
    public function login(array $data): bool {
        return true;
    }

    public function register(array $data): bool {
        return true;
    }

    public function logout(): bool {
        return true;
    }
}