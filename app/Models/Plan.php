<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Plan extends Model
{
    use HasFactory;

    protected $table = 'plans';

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'currency',
        'duration_days',
        'features',
        'limit_count',
        'is_active',
    ];

    /**
     * Attribute casting.
     */
    protected $casts = [
        'price' => 'decimal:2',
        'duration_days' => 'integer',
        'limit_count' => 'integer',
        'is_active' => 'boolean',
        'features' => 'array', // only if stored as JSON, otherwise remove this
    ];

    /**
     * Automatically generate slug if not provided.
     */
    protected static function boot()
    {
        parent::boot();

         function creating($plan) {
            if (empty($plan->slug)) {
                $plan->slug = Str::slug($plan->name);
            }
        };
    }

    /**
     * Scope to get only active plans.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * Check if the plan has unlimited usage.
     */
    public function isUnlimited(): bool
    {
        return $this->limit_count === 0;
    }
}
