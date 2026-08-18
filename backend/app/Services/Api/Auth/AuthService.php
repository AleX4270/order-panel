<?php
declare(strict_types=1);

namespace App\Services\Api\Auth;

use App\Exceptions\Api\Auth\InvalidCredentialsException;
use Illuminate\Http\Request;
use App\Http\Requests\Api\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class AuthService {
    public function getUserData(Request $request): Collection {
        $user = $request->user();

        return collect([
            'id' => $user->id,
            'name' => $user->name,
        ]);
    }

    public function login(LoginRequest $request, array $data): Collection {
        if(!Auth::attempt([...$data, 'is_active' => true])) {
            throw new InvalidCredentialsException;
        }

        $request->session()->regenerate();
        $user = Auth::guard('web')->user();

        return collect([
            'id' => $user->id,
            'name' => $user->name,
            'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
        ]);
    }

    public function logout(Request $request): bool {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return true;
    }
}
