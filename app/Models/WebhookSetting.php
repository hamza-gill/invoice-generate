<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookSetting extends Model
{
    use BelongsToOrganization, HasFactory;

    protected $table = 'webhook_settings';

    protected $fillable = [
        'organization_id',
        'webhook_url',
        'webhook_secret',

        // Customer events
        'enable_customer_create',
        'enable_customer_update',
        'enable_customer_delete',

        // Product events
        'enable_product_create',
        'enable_product_update',
        'enable_product_delete',

        // Invoice events
        'enable_invoice_create',
        'enable_invoice_update',
        'enable_invoice_delete',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        // Customer
        'enable_customer_create' => 'boolean',
        'enable_customer_update' => 'boolean',
        'enable_customer_delete' => 'boolean',

        // Product
        'enable_product_create' => 'boolean',
        'enable_product_update' => 'boolean',
        'enable_product_delete' => 'boolean',

        // Invoice
        'enable_invoice_create' => 'boolean',
        'enable_invoice_update' => 'boolean',
        'enable_invoice_delete' => 'boolean',
    ];

    /**
     * Scope to easily get the single settings record.
     */
    public static function settings()
    {
        return static::first() ?? new static();
    }
}
