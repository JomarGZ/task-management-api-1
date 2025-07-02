<?php

namespace App\Http\Controllers\api\v1\Chats;

use App\Enums\ChatTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\api\v1\Chats\StoreParticipantRequest;
use App\Http\Resources\api\v1\Chats\ChannelParticipantResource;
use App\Http\Resources\api\v1\tenants\TenantMemberResource;
use App\Models\Channel;
use Illuminate\Http\Request;

class ChannelParticipantsController extends Controller
{

    public function index(Channel $channel, Request $request)
    {
        $participants = $channel->participants()
            ->with('media')
            ->with(['media', 'directChannel' => function($q) {
                $q->select('channels.id')
                    ->withCount(['unreadMessages as unread_count']);
            }])
            ->where('users.id', '!=', auth()->id())
            ->search($request->input('query'))
            ->cursorPaginate(10);

        return TenantMemberResource::collection($participants);
    }
    public function store(StoreParticipantRequest $request, Channel $channel)
    {
        $channel->participants()->attach($request->participant_ids, [
            'tenant_id' => $request->user()->tenant_id
        ]);
        return ChannelParticipantResource::make($channel->load( 'participants'))->additional([
            'message' => 'Participants added successfully'
        ]);
    }

    public function destroy(Channel $channel, $userId)
    {
        abort_if(!$userId, 400,'User ID is required to remove from channel participants');
        abort_if(
            !$channel->participants()->where('users.id', $userId)->exists(),
             404,
             'User is not a participant in this channel'
        );
        $channel->participants()->detach($userId);
        return response()->noContent(); 
    }
}
