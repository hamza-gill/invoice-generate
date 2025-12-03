<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\Auth\LoginService;
use App\Services\Auth\RegisterService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected $registerService;

    public function __construct(RegisterService $registerService)
    {
        $this->registerService = $registerService;
    }

    public function register(RegisterRequest $request)
    {
        $response = $this->registerService->register($request->validated());

        if ($response['success']) {
            return redirect($response['redirect'])->with('success', $response['message']);
        }

        return back()->withInput()->with('error', $response['message']);
    }

    // Handle login form submission
    public function login(LoginRequest $request, LoginService $loginService)
    {

        $result = $loginService->authenticate($request);

        if ($result['success']) {
            return redirect()->intended($result['redirect_to'])->with('success', $result['message']);
        }

        return back()->withErrors($result['errors']);
    }

    // Handle logout
    public function logout(Request $request)
    {
        try {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/login')->with('success', 'You have been logged out.');
        } catch (Exception $e) {
            return back()->with('error', 'Logout failed. Please try again.');
        }
    }
}
