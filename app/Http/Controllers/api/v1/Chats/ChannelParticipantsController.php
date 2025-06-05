<?php

namespace App\Http\Controllers\api\v1\Chats;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\v1\Chats\StoreParticipantRequest;
use App\Http\Resources\api\v1\Chats\ChannelParticipantResource;
use App\Models\Channel;
use Illuminate\Http\Request;

class ChannelParticipantsController extends Controller
{
    public function store(StoreParticipantRequest $request, Channel $channel)
    {
        $channel->participants()->attach($request->participant_ids, [
            'tenant_id' => $request->user()->tenant_id
        ]);
        return ChannelParticipantResource::make($channel->load( 'participants'))->additional([
            'message' => 'Participants added successfully'
        ]);
    }
}
