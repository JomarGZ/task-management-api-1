<?php

namespace App\Models;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class ChannelParticipant extends Model
{
    //
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'channel_id', 'user_id', 'last_read_at'];

    protected $casts = [
        'last_read_at' => 'datetime',
    ];

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function scopeSearch($query, ?string $search = null)
    {
        return $query->when($search, function($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%");
        });
    }
}
