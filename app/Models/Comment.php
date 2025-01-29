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
        'content'
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

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

}
