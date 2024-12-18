<?php

namespace App\Traits;

use App\Models\Scopes\tenantScope;
use App\Models\Tenant;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        if (app()->runningInConsole()) {
            return;
        }
        static::addGlobalScope(new tenantScope);

        static::creating(function(Model $model){
            if (!auth()->check()) {
                abort(Response::HTTP_UNAUTHORIZED, 'You are not authenticate to perform this action');
            }
            $model->tenant_id = auth()->user()->tenant_id;
        });
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
