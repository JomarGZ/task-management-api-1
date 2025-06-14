<?php

namespace App\Http\Controllers\api\v1\Chats;

use App\Http\Controllers\Controller;
use App\Http\Resources\api\v1\Chats\ChannelResource;
use App\Models\Channel;
use App\Rules\NotEqualSender;
use Illuminate\Http\Request;

class DirectChannelController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'recipient_id' => [
                'required',
                'exists:users,id',
                new NotEqualSender(auth()->id())
                ]
            ]);

        return ChannelResource::make(Channel::direct($request->recipient_id)->load(['participants:id,name,position', 'participants.media']));
    }
}
