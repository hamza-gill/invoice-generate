<?php

namespace App\Http\Controllers;

use App\Mail\UserInvitationMail;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);
        $roles = config('roles'); // get roles from config file

        return view('users.index', compact('users', 'roles'));
    }


    public function invite(Request $request)
    {

        $request->validate([
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,manager,staff',
        ]);

        $token = Str::random(40);
        $role = strtolower($request->role);

        $user = User::create([
            'email' => $request->email,
            'role' => $role,
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
            'message' => 'Invitation sent successfully!'
        ]);
    }


    public function updateRole(Request $request, User $user)
    {
        $roles = config('roles');

        $request->validate([
            'role' => ['required', Rule::in($roles)],
        ]);

        $user->role = $request->role;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User role updated successfully.'
        ]);
    }

    public function revoke(User $user)
    {
        $user->status = 'inactive';
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User revoked successfully.'
        ]);
    }

}
