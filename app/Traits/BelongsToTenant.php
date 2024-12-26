<?php

namespace App\Traits;

use App\Models\Scopes\tenantScope;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        // if (app()->runningInConsole()) {
        //     return;
        // }
        static::addGlobalScope(new tenantScope);
      
        static::creating(function(Model $model){
            // if (app()->runningInConsole() || app()->environment('testing')) {
            //     return;
            // }
            if (self::shouldSkipTenantAssignmentIfRegisterRoute()) {
                return;
            }
            if (auth()->check()) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    protected static function shouldSkipTenantAssignmentIfRegisterRoute()
    {
        return request()->routeIs('register');
    }

  
}
