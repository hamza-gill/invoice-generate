<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlatformAdminAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('platform')->check()) {
            return redirect()->route('platform.settings');
        }

        return view('platform.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::guard('platform')->attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->route('platform.settings');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('platform')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('platform.login');
    }
}
