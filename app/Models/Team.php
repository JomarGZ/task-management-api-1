<?php

namespace App\Models;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    /** @use HasFactory<\Database\Factories\TeamFactory> */
    use HasFactory;

    protected $fillable = ['name'];

    public function members()
    {
        return $this->belongsToMany(
            User::class,
             'team_user', 
             'team_id', 
             'member_id'
             )
             ->withPivot('role')
             ->withTimestamps();
    }

    public function roles()
    {
        return $this->hasMany(Role::class);
    }
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function scopeSearch($query, $search)
    {
        return $query->when($search, function(Builder $query, $search) {
            $query->where('name', 'like', "%{$search}%");
        });
    }
}
