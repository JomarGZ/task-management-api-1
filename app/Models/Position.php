<?php

namespace App\Models;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    //
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'name'];
    
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
