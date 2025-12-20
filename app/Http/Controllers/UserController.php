<?php

namespace App\Http\Controllers;

use App\Mail\UserInvitationMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->latest()->paginate(10);
        return view('users.index', compact('users'));
    }

    public function invite(Request $request)
    {
        $this->authorize('create', User::class);
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:Admin,Manager,Staff',
        ]);

        $token = Str::random(40);

        $user = User::create([
            'email' => $request->email,
            'role' => $request->role,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'invitation_token' => $token,
            'password' => Hash::make('password123'),
            'invited_at' => now(),
            'is_active' => false,
        ]);

        Mail::to($request->email)->send(new UserInvitationMail($user));

        return response()->json([
            'success' => true,
            'message' => 'Invitation sent successfully!',
        ]);
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:Admin,Manager,Staff',
        ]);

        $user->update(['role' => $request->role]);

        return response()->json([
            'success' => true,
            'message' => 'User role updated successfully.',
        ]);
    }
}
