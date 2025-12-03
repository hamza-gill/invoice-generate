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
            if ($user->is_admin && $user->status == 'active') {
                return [
                    'success' => true,
                    'redirect_to' => route('admin.dashboard'),
                    'message' => 'Admin login successful.'
                ];
            }

            // Verified user
            if ($user->userVerification && $user->userVerification->status === 'approved') {
                return [
                    'success' => true,
                    'redirect_to' => route('dashboard'),
                    'message' => 'Login successful.'
                ];
            }

            // Unverified user
            return [
                'success' => true,
                'redirect_to' => route('docs.verification'),
                'message' => 'Please complete your ID verification.'
            ];

        } catch (Exception $e) {

            return [
                'success' => false,
                'errors' => ['error' => 'Login failed. Please try again later.'.$e->getMessage()]
            ];
        }
    }
}
