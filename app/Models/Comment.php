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
        'author_id',
        'commentable_id',
        'commentable_type',
        'content',
        'parent_id'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function(Comment $comment) {

            if (auth()->check()) {
                $comment->author_id = request()->user()->id;
            }
        });
    }

    public function commentable()
    {
        return $this->morphTo();
    }
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }
    
    public function parentComment()
    {
        return $this->belongsTo(Comment::class, 'commentable_id')
            ->where('commentable_type', self::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

}
