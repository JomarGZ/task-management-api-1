<?php

namespace App\Http\Controllers\api\v1\Chats;

use App\Http\Controllers\Controller;
use App\Http\Resources\api\v1\Chats\MessageResource;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageRepliesController extends Controller
{
    public function index(Message $message)
    {
        $replies = $message->replies()
            ->with('user')
            ->cursorPaginate(5);

        return MessageResource::collection($replies); 
    }
}
