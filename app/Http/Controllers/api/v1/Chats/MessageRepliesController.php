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
            ->select(['id', 'content', 'user_id', 'created_at', 'reaction_count', 'reply_count'])
            ->orderBy('created_at', 'desc')
            ->with(['user:id,name,position', 'user.media', 'likes'])    
            ->get();

        return MessageResource::collection($replies); 
    }
}
