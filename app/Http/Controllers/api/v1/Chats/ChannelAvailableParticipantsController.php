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
        $query = $request?->query('query');
        $participantsIds = (array)$request?->query('participant_ids') || [];
        if (empty($query)) return TenantMemberResource::collection([]);
        $participantIds = is_array($participantsIds) && !empty($participantsIds) 
            ? array_values(array_unique($participantsIds))
            : [];
        $participants = Channel::general()
            ->participants()
            ->with('media')
            ->select(['users.id', 'users.name', 'users.position'])
            ->when($participantIds, fn ($q) => $q->whereNotIn('users.id', [...$participantIds, auth()->id()]))
            ->where('users.name', 'LIKE', "%{$query}%")
            ->get();
        return TenantMemberResource::collection($participants);
    }
}
