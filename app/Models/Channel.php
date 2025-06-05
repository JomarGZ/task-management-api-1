<?php

namespace App\Models;
use App\Enums\ChatTypeEnum;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    //
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'name', 'description', 'user_id', 'type'];

    public function participants()
    {
        return $this->belongsToMany(User::class, 'channel_participants')
            ->withPivot('last_read_at')
            ->withTimestamps(); 
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function creatorOfChannel()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
   
    // public function user1()
    // {
    //     return $this->participants()->orderBy('id')->first();
    // }

    // public function user2()
    // {
    //     return $this->participants()->orderBy('id')->skip(1)->first();
    // }

    public function scopeDirectMessages($query, $userId)
    {
        return $query->where('type', ChatTypeEnum::DIRECT)
            ->whereHas('participants', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            });
    }

    public function scopeWithRepliesCount($query)
    {
        return $query->withCount('replies');
    }

    public static function general(): Channel
    {
        return self::where('type', ChatTypeEnum::GENERAL->value)->firstOrFail();
    }

    public static function group(int $channelId): Channel
    {
        return self::where('type', ChatTypeEnum::GROUP->value)->findOrFail($channelId);
    }

    public static function isGroupChannelMember(int $channelId): bool
    {
        return static::group($channelId)->participants()->where('user_id', auth()->id())->exists();
    }

    public static function isGeneralChannelMember()
    {
        return static::general()->participants()->where('user_id', auth()->id())->exists();
    }
}
