<?php

namespace App\Http\Controllers\api\v1\Chats;

use App\Enums\ChatTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\api\v1\Chats\StoreChannelRequest;
use App\Http\Resources\api\v1\Chats\ChannelResource;
use App\Models\Channel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ChannelController extends Controller
{
    public function index()
    {
        $channels = Channel::select('id', 'name', 'description', 'type')
            ->with('participants:id,name,position', 'participants.media')
            ->whereHas('participants', function ($q) {
                $q->where('users.id', auth()->id());
            })
            ->withCount('participants')
            ->where('active', true)
            ->where('type', ChatTypeEnum::GROUP->value)
            ->orderBy('created_at', 'asc')
            ->cursorPaginate(10);

        return ChannelResource::collection($channels);
    }

    public function store(StoreChannelRequest $request)
    {
      
        $channel = Channel::create([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'user_id' => $request->user()->id,
        ]);
        $channel->participants()->attach($request->user()->id, ['tenant_id' => $request->user()->tenant_id]);

        return new ChannelResource($channel);
    }

    public function show(Channel $channel) {
        return new ChannelResource($channel);
    }

    public function update(StoreChannelRequest $request, Channel $channel)
    {
        $channel->update($request->validated());

        return new ChannelResource($channel);
    }

    public function destroy(Channel $channel)
    {
        $channel->delete();

        return response()->noContent();
    }
}
