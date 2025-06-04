<?php

namespace App\Models;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    //
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'channel_id', 'user_id', 'content', 'parent_id', 'metadata', 'reaction_count', 'reply_count'];
    protected $casts = [
        'metadata' => 'array',
        'reaction_count' => 'integer',
        'reply_count' => 'integer',
    ];

    public function channel()
    {
        return $this->belongsTo(Channel::class);
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
}
