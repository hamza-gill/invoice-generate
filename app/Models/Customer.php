<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use App\Traits\WebhookEventTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use BelongsToOrganization, HasFactory, WebhookEventTrait;

    protected $fillable = [
        'organization_id',
        'stripe_customer_id',
        'first_name',
        'last_name',
        'company_name',
        'email',
        'phone_number',
        'address',
        'postal_code',
        'city',
        'state',
        'country',
    ];

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
