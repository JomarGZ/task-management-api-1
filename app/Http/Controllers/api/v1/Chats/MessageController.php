<?php

namespace App\Http\Controllers\api\v1\Chats;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\v1\Chats\TypeMessageRequest;
use App\Http\Resources\api\v1\Chats\MessageResource;
use App\Models\Channel;
use App\Services\v1\MessageHandlerFactory;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    
    public function store(TypeMessageRequest $request, MessageHandlerFactory $factory)
    {
        $handler = $factory->make($request->message_type);
        $data = $handler->validate($request);
        $channel = $this->resolveChannel($request);
        $message = $handler->handle($channel, $data);
        
        return new MessageResource($message)->additional([
            'message' => 'Message sent successfully',
        ]);
    }
    
    protected function resolveChannel(Request $request): Channel
    {
        switch ($request->message_type) {
            case 'general':
                return Channel::general(); 
            case 'direct': 
                // DM channel resolution logic
            case 'group':
                // Group channel resolution
            default:
                throw new \InvalidArgumentException("Unknown message type");
        }
    }
}
