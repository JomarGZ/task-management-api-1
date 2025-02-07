<?php

namespace App\Models;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    //
    use BelongsToTenant;

    public $timestamps = true;
    protected $fillable = ['tenant_id', 'user_id', 'assignable_id', 'assignable_type'];

    public function assignable()
    {
        return $this->morphTo();
    }


}
