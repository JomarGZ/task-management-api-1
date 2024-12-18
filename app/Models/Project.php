<?php

namespace App\Models;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'description'
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function scopeSearch($query, $search)
    {
        return $query->when($search, function(Builder $query, $search) {
            $query->whereAny([
                'name',
                'description'
            ], 'like', "%{$search}%");
        });
    }


}
