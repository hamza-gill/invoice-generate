<?php

namespace App\Models;

use App\Services\TenantContext;
use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    use BelongsToOrganization;

    protected $fillable = [
        'organization_id',
        'company_name',
        'tax_id',
        'country',
        'base_currency',
        'address',
        'logo_path',
        'invoice_notes',
        'invoice_terms',
        'tax_percentage',
        'stripe_public_key',
        'stripe_secret_key',
        'payment_gateway_enabled',
        'webhook_url',
        'webhook_secret',
        'contact_email',
        'enable_terms',
        'enable_invoice_notes',
        'enable_tax',
        'enable_tax_id',
        'enable_due_date',
        'enable_rush_delivery',
        'rush_delivery_options',
        'starting_invoice_number',
        'google_places_key',
        'default_template_id',
        'custom_invoice_css',
        'mail_mailer',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'mail_from_address',
        'mail_from_name',
    ];

    protected $casts = [
        'enable_terms' => 'boolean',
        'enable_invoice_notes' => 'boolean',
        'enable_tax' => 'boolean',
        'enable_tax_id' => 'boolean',
        'enable_due_date' => 'boolean',
        'enable_rush_delivery' => 'boolean',
        'payment_gateway_enabled' => 'boolean',
        'rush_delivery_options' => 'array',
        'tax_percentage' => 'decimal:2',
    ];

    protected static function booted()
    {
        // Handle cache refresh automatically when settings change
        static::saved(function ($setting) {
            if ($setting->organization_id) {
                Cache::forget('app_settings_' . $setting->organization_id);
            }
        });

        static::deleted(function ($setting) {
            if ($setting->organization_id) {
                Cache::forget('app_settings_' . $setting->organization_id);
            }
        });
    }

    // ✅ Automatically decrypt when accessed
    public function getGooglePlacesKeyAttribute($value)
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    // ✅ Automatically encrypt when saved
    public function setGooglePlacesKeyAttribute($value)
    {
        $this->attributes['google_places_key'] = $value ? Crypt::encryptString($value) : null;
    }

    // ✅ Automatically decrypt when accessed
    public function getMailPasswordAttribute($value)
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    // ✅ Automatically encrypt when saved
    public function setMailPasswordAttribute($value)
    {
        $this->attributes['mail_password'] = $value ? Crypt::encryptString($value) : null;
    }

    /**
     * Whether the organization has configured its own outbound mail.
     */
    public function hasCustomMailConfig()
    {
        return !empty($this->mail_mailer) && $this->mail_mailer !== 'platform_default';
    }

    /**
     * Get default rush delivery options
     *
     * @return array
     */
    public function getDefaultRushOptions()
    {
        return [
            [
                'label' => 'Express (2 days)',
                'days' => 2,
                'fee' => 295
            ],
            [
                'label' => 'Fast (3 days)',
                'days' => 3,
                'fee' => 195
            ],
            [
                'label' => 'Quick (4 days)',
                'days' => 4,
                'fee' => 95
            ],
            [
                'label' => 'Standard (5-7 days)',
                'days' => 'standard',
                'fee' => 0
            ],
        ];
    }

    /**
     * Get rush delivery options with fallback to defaults
     *
     * @return array
     */
    public function getRushOptionsAttribute()
    {
        // If rush_delivery_options is null or empty, return defaults
        if (empty($this->rush_delivery_options)) {
            return $this->getDefaultRushOptions();
        }

        return $this->rush_delivery_options;
    }

    /**
     * Check if rush delivery is enabled and has options
     *
     * @return bool
     */
    public function hasRushDelivery()
    {
        return $this->enable_rush_delivery && !empty($this->rush_options);
    }

    /**
     * Get rush delivery option by days
     *
     * @param string|int $days
     * @return array|null
     */
    public function getRushOptionByDays($days)
    {
        $options = $this->rush_options;

        foreach ($options as $option) {
            if ($option['days'] == $days) {
                return $option;
            }
        }

        return null;
    }

    /**
     * Get standard delivery option (fee = 0)
     *
     * @return array|null
     */
    public function getStandardDeliveryOption()
    {
        $options = $this->rush_options;

        foreach ($options as $option) {
            if ($option['fee'] == 0 || $option['days'] === 'standard') {
                return $option;
            }
        }

        return null;
    }
}
