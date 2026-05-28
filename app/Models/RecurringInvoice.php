<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecurringInvoice extends Model
{
    use BelongsToOrganization;

    protected $fillable = [
        'organization_id', 'customer_id', 'user_id', 'invoice_template_id',
        'title', 'description', 'amount', 'discount', 'frequency',
        'start_date', 'end_date', 'next_send_date', 'last_sent_at',
        'total_sent', 'max_occurrences', 'status', 'line_items',
        'notes', 'project_address', 'auto_send_email',
    ];

    protected $casts = [
        'line_items' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'next_send_date' => 'date',
        'last_sent_at' => 'date',
        'auto_send_email' => 'boolean',
        'amount' => 'decimal:2',
        'discount' => 'decimal:2',
    ];

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

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function isPaused(): bool
    {
        return $this->status === 'paused';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function calculateNextSendDate(): ?string
    {
        $last = $this->next_send_date ?? $this->start_date;
        if (!$last) return null;
        return match ($this->frequency) {
            'weekly' => $last->copy()->addWeek()->toDateString(),
            'biweekly' => $last->copy()->addWeeks(2)->toDateString(),
            'monthly' => $last->copy()->addMonth()->toDateString(),
            'quarterly' => $last->copy()->addMonths(3)->toDateString(),
            'yearly' => $last->copy()->addYear()->toDateString(),
            default => null,
        };
    }
}
