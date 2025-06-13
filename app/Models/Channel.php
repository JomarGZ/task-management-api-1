<?php

namespace App\Models;
use App\Enums\ChatTypeEnum;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Channel extends Model
{
    //
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'name', 'description', 'user_id', 'type', 'active'];

    protected static function booted()
    {
        static::creating(function($channel) {
            $channel->active = $channel->type !== ChatTypeEnum::GENERAL->value ? true : false;
        });
    }
    public function participants()
    {
        return $this->belongsToMany(User::class, 'channel_participants')
            ->withPivot('last_read_at')
            ->withTimestamps(); 
    }

    public function messages()
    {
        return $this->hasMany(Message::class)
            ->whereNull('parent_id')
            ->with('user');
    }

    public function creatorOfChannel()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
   
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

    public static function direct(int $recipientId)
    {
        $authId = auth()->id();
        if ($authId === $recipientId) {
            throw new \InvalidArgumentException('Cannot start a DM with yourself.');
        }

        return self::firstOrCreateDirectChannel($authId, $recipientId);
    }

    private static function firstOrCreateDirectChannel(int $user1Id, int $user2Id): Channel
    {
        $channel = self::where('type', ChatTypeEnum::DIRECT->value)
            ->where(function ($query) use ($user1Id, $user2Id): void{
                $query->whereHas('participants', fn($q) => $q->where('user_id', $user1Id))
                    ->whereHas('participants', fn($q) => $q->where('user_id', $user2Id));
            })->first();

        if ($channel) {
            if(!$channel->active) {
                $channel->update(['active' => true]);
            }
            return $channel->fresh();
        }
        try {
            DB::beginTransaction();
            $channel = self::create([
                'user_id' => auth()->id(),
                'type' => ChatTypeEnum::DIRECT->value,
                'name' => "DB:$user1Id-$user2Id",
            ]);
            $channel->participants()->attach([$user1Id, $user2Id], ['tenant_id' => auth()->user()->tenant_id]);
            DB::commit();
            return $channel;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error on creating channel and add participant in direct type: {$e->getMessage()}", ['exception' => $e]);
            throw new \RuntimeException("Failed to create direct message channel.");
        }
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
