<?php

namespace App\Models;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamUser extends Model
{
    //
    use BelongsToTenant, HasFactory;
    
    protected $table = 'team_user';
    protected $fillable = [
        'tenant_id',
        'team_id',
        'member_id',
        'role'
    ];
}
