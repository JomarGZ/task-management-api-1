<?php

namespace App\Http\Controllers\api\v1\Chats;

use App\Http\Controllers\Controller;
use App\Http\Resources\api\v1\tenants\TenantMemberResource;
use App\Models\Channel;
use Illuminate\Http\Request;

class ChannelAvailableParticipantsController extends Controller
{
    public function index(Request $request)
    {
        $participantIds = $request->query('participant_ids');
        $query = $request?->query('query');
        if (empty($query)) return TenantMemberResource::collection([]);

        $participantIds = $participantIds ? array_map("intval", explode(',', $participantIds)) : [];
        $participantIds = is_array($participantIds) && !empty($participantIds) 
            ? array_values(array_unique($participantIds))
            : [];
            
        $participants = Channel::general()
            ->participants()
            ->with('media')
            ->whereNot('users.id', auth()->id())
            ->select(['users.id', 'users.name', 'users.position'])
            ->when($participantIds, fn ($q) => $q->whereNotIn('users.id', $participantIds))
            ->where('users.name', 'LIKE', "%{$query}%")
            ->get();
        return TenantMemberResource::collection($participants);
    }
}
