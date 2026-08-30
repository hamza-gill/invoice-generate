<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportMessage extends Model
{
    protected $fillable = [
        'support_ticket_id',
        'sender_type',
        'sender_id',
        'body',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'support_ticket_id');
    }

    public function sender(): ?Model
    {
        return $this->sender_type === 'admin'
            ? PlatformAdmin::find($this->sender_id)
            : User::find($this->sender_id);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(SupportAttachment::class, 'support_message_id');
    }
}
