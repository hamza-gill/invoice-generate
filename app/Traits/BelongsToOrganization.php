<?php

namespace App\Traits;

use App\Models\Organization;
use App\Services\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToOrganization
{
    public static function bootBelongsToOrganization(): void
    {
        static::addGlobalScope('organization', function (Builder $builder) {
            $organizationId = app(TenantContext::class)->id();

            if ($organizationId) {
                $builder->where(
                    $builder->getModel()->getTable() . '.organization_id',
                    $organizationId
                );
            }
        });

        static::creating(function ($model) {
            if (! $model->organization_id) {
                $model->organization_id = app(TenantContext::class)->id();
            }
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
