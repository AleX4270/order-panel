<?php
declare(strict_types=1);

namespace App\Services\Api\Auth;

use App\Exceptions\UserAlreadyExistsException;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService {
    public function login(LoginRequest $request, array $data): ?User {
        if(!Auth::attempt($data)) {
            return null;
        }

        $request->session()->regenerate();
        return Auth::guard('web')->user();
    }

    public function register(array $data): bool {
        $existingUser = User::where('name', $data['name'])
            ->orWhere('email', $data['email'])
            ->first();

        if(!empty($existingUser)) {
            throw new UserAlreadyExistsException;
        }

        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        $user->save();

        return true;
    }

    public function logout(Request $request): bool {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return true;
    }
}
