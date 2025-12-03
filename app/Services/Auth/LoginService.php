<?php

namespace App\Services\Auth;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginService
{
    public function authenticate(Request $request): array
    {
        try {
            $credentials = $request->only('email', 'password');

            if (!Auth::attempt($credentials)) {
                return [
                    'success' => false,
                    'errors' => ['email' => 'The provided credentials do not match our records.']
                ];
            }

            $request->session()->regenerate();

            $user = Auth::user();

            // Admin redirection
            if ($user->email_verified_at && $user->status == 'active') {
                return [
                    'success' => true,
                    'redirect_to' => route('admin.dashboard'),
                    'message' => 'Admin login successful.'
                ];
            }


            // Unverified user
            return [
                'success' => true,
                'redirect_to' => route('login'),
                'message' => 'Please verify your email to login.'
            ];

        } catch (Exception $e) {

            return [
                'success' => false,
                'errors' => ['error' => 'Login failed. Please try again later.'.$e->getMessage()]
            ];
        }
    }
}
