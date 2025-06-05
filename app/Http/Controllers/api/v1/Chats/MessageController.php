<?php

namespace App\Http\Controllers\api\v1\Chats;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\v1\Chats\TypeMessageRequest;
use App\Http\Requests\api\v1\Chats\UpdateGeneralMessageRequest;
use App\Http\Resources\api\v1\Chats\MessageResource;
use App\Models\Channel;
use App\Models\Message;
use App\Services\v1\MessageHandlerFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MessageController extends Controller
{
    protected MessageHandlerFactory $handlerFactory;
    public function __construct(MessageHandlerFactory $handlerFactory)
    {
        $this->handlerFactory = $handlerFactory;
    }

    public function index(Channel $channel ,Request $request)
    {
        $messages = $channel->messages()
            ->with('user')
            ->forChannel($channel)
            ->cursorPaginate(3);

        return MessageResource::collection($messages)->additional([
            'message' => 'Messages retrieved successfully'
        ]);
    }
 
    public function store(TypeMessageRequest $request)
    {
        $handler = $this->handlerFactory->make($request->message_type);
        $channel = $handler->resolveChannel($request);
        $data = $handler->validateStore($request);
        
        $message = $handler->handle($channel, $data);
        
        return new MessageResource($message)->additional([
            'message' => 'Message sent successfully',
        ]);
    }

    public function update(Message $message, TypeMessageRequest $request)
    {

        $handler = $this->handlerFactory->make($request->message_type);
        $data = $handler->validateUpdate($request);
        $message->update($data);

        return MessageResource::make($message)->additional([
            'message' => 'Message updated successfully',
        ]);
    }

    public function destroy(Message $message)
    {
        Gate::authorize('delete', $message);
        $message->delete();

        return response()->noContent();
    }

}
