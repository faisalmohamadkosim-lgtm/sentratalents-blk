<?php

namespace App\Models\Concerns;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            // tenant_id null = platform-level user (superadmin/ultraadmin) -- must see
            // ALL tenants unscoped, not be scoped to "tenant_id = NULL" (which matches
            // nothing and would silently hide every row from them).
            if (($user = auth()->user()) && !is_null($user->tenant_id)) {
                $builder->where($builder->getModel()->getTable().'.tenant_id', $user->tenant_id);
            }
        });

        static::creating(function (Model $model) {
            if (is_null($model->tenant_id) && ($user = auth()->user())) {
                $model->tenant_id = $user->tenant_id;
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }
}
