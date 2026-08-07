<?php

namespace App\Http\Controllers;

use App\Models\PlatformSetting;
use Illuminate\Http\Request;

class PlatformAdminSettingController extends Controller
{
    public function index()
    {
        $platformSettings = PlatformSetting::record() ?? new PlatformSetting();

        $secretKey = PlatformSetting::getStripeSecretKey();
        $webhookSecret = PlatformSetting::getStripeWebhookSecret();

        $sources = [
            'stripe_secret_key' => $platformSettings->stripe_secret_key
                ? 'Stored in database'
                : (config('subscription.stripe_secret') ? 'From .env (fallback)' : 'Not configured'),
            'stripe_webhook_secret' => $platformSettings->stripe_webhook_secret
                ? 'Stored in database'
                : (config('subscription.stripe_webhook_secret') ? 'From .env (fallback)' : 'Not configured'),
            'stripe_publishable_key' => $platformSettings->stripe_publishable_key
                ? 'Stored in database'
                : (config('subscription.stripe_key') ? 'From .env (fallback)' : 'Not configured'),
        ];

        $hasSecretKey = (bool) $secretKey;

        return view('platform.settings', compact('platformSettings', 'hasSecretKey', 'sources'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'stripe_publishable_key'  => ['nullable', 'string', 'max:255'],
            'stripe_secret_key'       => ['nullable', 'string', 'max:255'],
            'stripe_webhook_secret'   => ['nullable', 'string', 'max:255'],
        ]);

        $record = PlatformSetting::firstOrNew(['id' => 1]);

        // Only overwrite a key when a new value is provided, so secrets stay hidden.
        foreach (['stripe_publishable_key', 'stripe_secret_key', 'stripe_webhook_secret'] as $field) {
            if (!empty($data[$field])) {
                $record->{$field} = $data[$field];
            }
        }

        if ($record->stripe_secret_key) {
            $record->stripe_enabled = true;
            $record->stripe_connected_at = $record->stripe_connected_at ?? now();
        }

        $record->id = 1;
        $record->save();

        return back()->with('success', 'Platform settings saved successfully.');
    }
}
