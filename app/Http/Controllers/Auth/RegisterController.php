<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;

class RegisterController extends Controller
{
    public function __construct(protected AuthService $authService)
    {
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $this->authService->register($request->validated(), $request);

        return redirect('/dashboard');
    }
}
