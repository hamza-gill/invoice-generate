<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceTemplate extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'thumbnail', 'html_layout',
        'css_styles', 'config', 'is_system', 'is_active', 'sort_order',
        'organization_id',
    ];

    protected $casts = [
        'config' => 'array',
        'is_system' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function scopeAvailableFor($query, ?int $organizationId)
    {
        return $query->where('is_active', true)
            ->where(function ($q) use ($organizationId) {
                $q->whereNull('organization_id')
                  ->orWhere('organization_id', $organizationId);
            });
    }
}
