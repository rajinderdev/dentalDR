<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function login(array $credentials, bool $remember, Request $request): array
    {
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // if (!auth()->user()->is_admin) {
            //     Auth::logout();

            //     return [
            //         'success' => false,
            //         'error' => 'You do not have admin access.',
            //     ];
            // }

            return ['success' => true, 'error' => null];
        }

        return [
            'success' => false,
            'error' => 'The provided credentials do not match our records.',
        ];
    }

    public function register(array $data, Request $request): User
    {
        $user = User::create($data);

        Auth::login($user);

        return $user;
    }

    public function logout(Request $request): void
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
