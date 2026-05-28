<?php

namespace App\Services\Auth;

use App\Mail\SendOtpVerificationMail;
use App\Models\Organization;
use App\Models\Setting;
use App\Models\User;
use App\Services\SubscriptionService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RegisterService
{
    public function __construct(protected SubscriptionService $subscriptionService)
    {
    }

    public function register(array $data): array
    {
        try {
            return DB::transaction(function () use ($data) {
                $organization = Organization::create([
                    'name' => $data['company_name'],
                    'slug' => Str::slug($data['company_name']) . '-' . Str::lower(Str::random(6)),
                    'email' => $data['email'],
                    'phone' => $data['phone'] ?? null,
                    'status' => 'trial',
                ]);

                $user = User::create([
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'] ?? null,
                    'password' => Hash::make($data['password']),
                    'organization_id' => $organization->id,
                    'role' => User::ROLE_ADMIN,
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]);

                $organization->update(['owner_id' => $user->id]);

                Setting::withoutGlobalScopes()->create([
                    'organization_id' => $organization->id,
                    'company_name' => $data['company_name'],
                    'contact_email' => $data['email'],
                    'base_currency' => 'USD',
                    'country' => 'United States',
                    'payment_gateway_enabled' => false,
                ]);

                $this->subscriptionService->startTrial($organization);

                Auth::login($user);

                return [
                    'success' => true,
                    'message' => 'Registration successful! Your 14-day trial has started.',
                    'redirect' => route('dashboard'),
                ];
            });
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Registration failed. ' . $e->getMessage(),
            ];
        }
    }

    private function sendVerificationMail(string $email): void
    {
        $otp = rand(100000, 999999);

        DB::table('user_tokens')->updateOrInsert(
            ['email' => $email, 'type' => 'email_verification'],
            [
                'token' => Hash::make($otp),
                'created_at' => now(),
            ]
        );

        Mail::to($email)->send(new SendOtpVerificationMail($otp, $email));
    }
}
