<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use App\Traits\WebhookEventTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use BelongsToOrganization, HasFactory, WebhookEventTrait;

    protected $fillable = [
        'organization_id',
        'name',
        'description',
        'price',
        'category',
        'is_active'
    ];

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
