<?php

namespace App\Http\Controllers\api\v1\Chats;

use App\Enums\ChatTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\api\v1\Chats\StoreChannelRequest;
use App\Http\Requests\api\v1\Chats\UpdateChannelRequest;
use App\Http\Resources\api\v1\Chats\ChannelResource;
use App\Models\Channel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class ChannelController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        
        $channels = Channel::select('id', 'name', 'description', 'type')
            ->with('participants:id,name,position', 'participants.media')
            ->whereHas('participants', fn($q) => $q->where('users.id', $userId))
            ->withCount('participants')
            ->withCount(['unreadMessages as unread_messages_count'])
            ->where('active', true)
            ->where('type', ChatTypeEnum::GROUP->value)
            ->orderBy('created_at')
            ->cursorPaginate(10);
        return ChannelResource::collection($channels);

    }

    public function store(StoreChannelRequest $request)
    {
        try {
            DB::beginTransaction();
            $channel = Channel::create([
                'name' => $request->name,
                'description' => $request->description,
                'type' => ChatTypeEnum::GROUP->value,
                'user_id' => $request->user()->id,
            ]);
            $participantIds = isset($request->participant_ids) && !empty($request->participant_ids) ? array_values(array_unique($request->participant_ids)) : [];
            $channel->participants()->attach($request->user()->id, ['tenant_id' => $request->user()->tenant_id]);
            if ($participantIds) {
                $channel->participants()->attach($participantIds, [
                        'tenant_id' => $request->user()->tenant_id
                    ]);
            }

            Db::commit();
            return new ChannelResource($channel->load('participants:id,position,name', 'participants.media'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Unexpected error creating channel: ', ['exceptions' => $e->getMessage()]);
            return response()->json([
                'message' => 'An unexpected error occurred'
            ], 500);
        }
      
    }

    public function show(Channel $channel) {
        return ChannelResource::make($channel->load('participants:id,position,name', 'participants.media'));
           
    }

    public function update(UpdateChannelRequest $request, Channel $channel)
    {
        DB::beginTransaction();
        try {
            $channel->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            $participantIds = isset($request->participant_ids) 
                && !empty($request->participant_ids) 
                ? array_values(array_unique($request->participant_ids)) 
                : [];

            if (!empty($participantIds)) {
                $channel->participants()->attach($participantIds, [
                    'tenant_id' => $request->user()->tenant_id,
                ]);
            }
            DB::commit(); 
            return new ChannelResource($channel->fresh()->load('participants:id,position,name', 'participants.media'));

        } catch (Exception $e) {
            DB::rollBack(); 
            return response()->json([
                'message' => 'Failed to update channel: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Channel $channel)
    {
        $channel->delete();

        return response()->noContent();
    }
}
