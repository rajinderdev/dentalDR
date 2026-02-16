<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function __construct(protected AuthService $authService)
    {
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $result = $this->authService->login(
            $request->only('email', 'password'),
            $request->boolean('remember'),
            $request,
        );

        if (!$result['success']) {
            return back()->withErrors([
                'email' => $result['error'],
            ])->onlyInput('email');
        }

        return redirect()->intended('/dashboard');
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request);

        return redirect('/login');
    }
}
