<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeatureRequest extends Model
{
    public const TYPES = [
        'feature_request' => 'Feature enhancement',
        'new_module' => 'New module',
        'integration' => 'Integration',
        'general_inquiry' => 'General inquiry',
    ];

    public const PRIORITIES = [
        'low' => 'Nice to have',
        'normal' => 'Important',
        'high' => 'Critical for our workflow',
    ];

    protected $fillable = [
        'user_id',
        'organization_id',
        'name',
        'email',
        'company',
        'phone',
        'request_type',
        'module_name',
        'title',
        'requirements',
        'use_case',
        'priority',
        'status',
        'ip_address',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->request_type] ?? ucfirst(str_replace('_', ' ', $this->request_type));
    }
}
