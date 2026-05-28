<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Estimate extends Model
{
    use BelongsToOrganization;

    protected $fillable = [
        'organization_id', 'customer_id', 'user_id', 'invoice_template_id',
        'estimate_number', 'description', 'amount', 'discount',
        'issue_date', 'valid_until', 'status', 'notes', 'project_address',
        'converted_invoice_id', 'approved_at', 'declined_at',
        'client_token', 'custom_fields',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'valid_until' => 'date',
        'approved_at' => 'datetime',
        'declined_at' => 'datetime',
        'custom_fields' => 'array',
        'amount' => 'decimal:2',
        'discount' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function ($estimate) {
            if (!$estimate->client_token) {
                $estimate->client_token = Str::random(64);
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(InvoiceTemplate::class, 'invoice_template_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(EstimateItem::class);
    }

    public function convertedInvoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'converted_invoice_id');
    }

    public function isExpired(): bool
    {
        return $this->valid_until && $this->valid_until->isPast();
    }

    public function canBeApproved(): bool
    {
        return in_array($this->status, ['sent', 'viewed']) && !$this->isExpired();
    }

    public static function generateEstimateNumber(): string
    {
        $year = date('Y');
        $last = static::withoutGlobalScopes()
            ->where('estimate_number', 'like', "EST-{$year}-%")
            ->orderByRaw('CAST(SUBSTRING_INDEX(estimate_number, "-", -1) AS UNSIGNED) DESC')
            ->first();

        if ($last && preg_match('/EST-\d{4}-(\d+)/', $last->estimate_number, $m)) {
            $next = (int) $m[1] + 1;
        } else {
            $next = 1;
        }

        return sprintf('EST-%s-%03d', $year, $next);
    }
}
