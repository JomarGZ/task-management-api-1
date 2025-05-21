<?php

namespace App\Models;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    /** @use HasFactory<\Database\Factories\CommentFactory> */
    use HasFactory;
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'commentable_id',
        'commentable_type',
        'content',
        'parent_id'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function commentable()
    {
        return $this->morphTo();
    }
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
        // ->with('user', 'replies');
    }

    public function scopeForModel($query, $model)
    {
        return $query->where('commentable_id', $model->id)
            ->where('commentable_type', get_class($model));
    }

    public function scopeWithReplies($query)
    {
        return $query->with(['user', 'replies.user']);
    }

    public function scopeTopLevelOnly($query)
    {
        return $query->whereNull('parent_id');
    }

}
