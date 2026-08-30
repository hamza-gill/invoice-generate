<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    use BelongsToOrganization;

    protected $fillable = [
        'organization_id',
        'subject',
        'priority',
        'status',
        'opened_by',
        'assigned_to',
        'last_message_at',
        'is_read_by_admin',
        'is_read_by_org',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'is_read_by_admin' => 'boolean',
        'is_read_by_org' => 'boolean',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(SupportMessage::class)->orderBy('created_at');
    }

    public function opener(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function lastMessage(): ?SupportMessage
    {
        return $this->messages()->latest()->first();
    }

    public static function priorities(): array
    {
        return ['low', 'medium', 'high', 'urgent'];
    }

    public static function statuses(): array
    {
        return ['open', 'in_progress', 'resolved', 'closed'];
    }
}
