<?php

namespace App\Http\Controllers\api\v1\Chats;

use App\Http\Controllers\Controller;
use App\Http\Resources\api\v1\Chats\ChannelResource;
use App\Models\Channel;
use Illuminate\Http\Request;

class GeneralChannelController extends Controller
{
    public function show()
    {
        return ChannelResource::make(Channel::general());
    }
}
