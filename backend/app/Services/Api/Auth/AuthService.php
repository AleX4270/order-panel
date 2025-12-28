<?php
declare(strict_types=1);

namespace App\Services\Api\Auth;

use App\Exceptions\UserAlreadyExistsException;
use Illuminate\Http\Request;
use App\Http\Requests\Api\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class AuthService {
    public function getUserData(Request $request): Collection {
        $user = $request->user();
        // $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();

        return collect([
            'id' => $user->id,
            'name' => $user->name,
            // 'permissions' => $userPermissions,
        ]);
    }

    public function login(LoginRequest $request, array $data): ?Collection {
        $requestUser = User::where('email', $data['email'])->first();
        if(empty($requestUser) || empty($requestUser->is_active)) {
            return null;
        }

        if(!Auth::attempt($data)) {
            return null;
        }

        $request->session()->regenerate();

        $user = Auth::guard('web')->user();
        $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();

        return collect([
            'id' => $user->id,
            'name' => $user->name,
            'permissions' => $userPermissions,
        ]);
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
