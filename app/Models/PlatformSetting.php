<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

/**
 * Global platform-level settings (admin Stripe keys for subscriptions).
 *
 * The secret keys are stored encrypted and are used by the platform's own
 * Stripe account for subscription checkout. If no value has been saved in
 * the database, the config / env values are used as a fallback.
 */
class PlatformSetting extends Model
{
    protected $fillable = [
        'stripe_publishable_key',
        'stripe_secret_key',
        'stripe_webhook_secret',
        'stripe_enabled',
        'stripe_connected_at',
    ];

    protected $casts = [
        'stripe_enabled' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('platform_settings');
        });
    }

    public function getStripeSecretKeyAttribute($value)
    {
        return $this->decryptSecret($value);
    }

    public function setStripeSecretKeyAttribute($value)
    {
        $this->attributes['stripe_secret_key'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getStripeWebhookSecretAttribute($value)
    {
        return $this->decryptSecret($value);
    }

    public function setStripeWebhookSecretAttribute($value)
    {
        $this->attributes['stripe_webhook_secret'] = $value ? Crypt::encryptString($value) : null;
    }

    /**
     * Decrypt a secret, falling back to the raw value for legacy plaintext rows.
     */
    protected function decryptSecret(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Throwable $e) {
            return $value;
        }
    }

    /**
     * Get the single platform settings record (cached).
     */
    public static function record(): ?self
    {
        return Cache::remember('platform_settings', 3600, function () {
            return static::query()->first();
        });
    }

    public static function getStripePublicKey(): ?string
    {
        return static::record()?->stripe_publishable_key ?: config('subscription.stripe_key');
    }

    public static function getStripeSecretKey(): ?string
    {
        return static::record()?->stripe_secret_key ?: config('subscription.stripe_secret');
    }

    public static function getStripeWebhookSecret(): ?string
    {
        return static::record()?->stripe_webhook_secret ?: config('subscription.stripe_webhook_secret');
    }

    public static function isStripeEnabled(): bool
    {
        return (bool) static::record()?->stripe_enabled;
    }
}
