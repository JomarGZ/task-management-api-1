<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\Enums\Statuses;
use App\Traits\BelongsToTenant;
use App\Enums\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class User extends Authenticatable implements HasMedia
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, BelongsToTenant, InteractsWithMedia, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'position',
        'role',
        'tenant_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'tenant_id'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->singleFile()
            ->useDisk(config('filesystems.default'))
            ->registerMediaConversions(function (Media $media) {
                $this->addMediaConversion('thumb-200')
                    ->width(200)
                    ->height(200)
                    ->sharpen(10);
    
                $this->addMediaConversion('thumb-60')
                    ->width(60)
                    ->height(60)
                    ->sharpen(3);
            });
           
    }
    
    public function tasks()
    {
        return $this->belongsToMany(Task::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function activeTasks()
    {
        return $this->tasks()->where('status', '!=', Statuses::COMPLETED->value);
    }

    public function teams()
    {
        return $this->belongsToMany(
            Team::class,
            'team_user',
            'member_id',
            'team_id'
        )->withPivot('role')
         ->withTimestamps();
    }

    public function assignedProjectAsManager()
    {
        return $this->hasMany(Project::class, 'manager');
    }

    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_dev_id');
    }

    public function scopeSearch($query, $search) {
        return $query->when($search, function (Builder $query, $search) {
            $query->whereAny([
                'name',
                'email',
            ], 'like', "%{$search}%");
        });
    }

    public function scopeFilterByRole($query, $role)
    {
        return $query->when($role, function (Builder $query, $role) {
            $query->where('role', $role);
        });
    }

    public function isAdmin()
    {
        return $this->role === Role::ADMIN->value;
    }

    public function belongsToTenant(Model $model)
    {
        if ($model?->tenant_id) {
            return $this->tenant_id === $model->tenant_id;
        }
        return false;
    }

    public function isTeamLead($teamMemberPivotId)
    {
        return $this->teams()->where('id', $teamMemberPivotId)
            ->wherePivot('role', Role::TEAM_LEAD->value)
            ->exists();
    }
    public function isProjectManager()
    {   
        return $this
                    ->teams()
                    ->wherePivot('role', Role::PROJECT_MANAGER->value)
                    ->exists();
    }

    public function isOwnerOfComment($comment)
    {
        return $this->id === $comment->author_id;
    }
    
}
