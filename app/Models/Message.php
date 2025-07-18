<?php

namespace App\Models;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    //
    use BelongsToTenant, SoftDeletes;

    protected $fillable = ['tenant_id', 'channel_id', 'user_id', 'content', 'parent_id', 'metadata', 'reaction_count', 'reply_count', 'read', 'read_at'];
    
    protected static function booted()
    {
        static::created(function(Message $message) {
            if ($message->parent_id) {
                $message->parent->increment('reply_count');
            }
        });

        static::deleted(function(Message $message) {
            if($message->parent_id) {
                $message->parent->decrement('reply_count');
            }
        });
    }
    protected $casts = [
        'metadata' => 'array',
        'reaction_count' => 'integer',
        'reply_count' => 'integer',
    ];

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function readers()
    {
        return $this->belongsToMany(User::class, 'message_reads')
                    ->withPivot('read_at')
                    ->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function parent()
    {
        return $this->belongsTo(Message::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Message::class, 'parent_id')->latest();
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'message_likes');
    }

    public function isLikeBy(?int $userId)
    {
        if (!$userId) return false;
        if ($this->relationLoaded('likes')) {
            return $this->likes->contains('id', $userId);
        }
        return $this->likes()->where('user_id', $userId)->exists();
    }
    public function scopeForChannel($query, Channel $channel)
    {
        return $query->where('channel_id', $channel->id)
            ->with('user')
            ->latest();
    }

}
