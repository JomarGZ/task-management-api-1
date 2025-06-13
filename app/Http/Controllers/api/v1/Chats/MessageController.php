<?php

namespace App\Http\Controllers\api\v1\Chats;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\v1\Chats\TypeMessageRequest;
use App\Http\Requests\api\v1\Chats\UpdateGeneralMessageRequest;
use App\Http\Resources\api\v1\Chats\MessageResource;
use App\Models\Channel;
use App\Models\Message;
use App\Services\v1\MessageHandlerFactory;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

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
            ->select(['id', 'content', 'user_id', 'created_at', 'reaction_count', 'reply_count'])
            ->with(['user:id,name,position', 'user.media', 'likes:id,name', 'likes.media'])
            ->forChannel($channel)
            ->orderBy('created_at', 'desc')
            ->cursorPaginate(10);

        return MessageResource::collection($messages)->additional([
            'message' => 'Messages retrieved successfully'
        ]);
    }
 
    public function store(TypeMessageRequest $request)
    {
        try {
            $handler = $this->handlerFactory->make($request->message_type);
            $data = $handler->validateStore($request);
            $channel = $handler->resolveChannel($request);
        
            $message = $handler->handle($channel, $data);
            
            return new MessageResource($message->load(['user:id,name,position', 'user.media', 'likes']))->additional([
                'message' => 'Message sent successfully',
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send a message',
                'error' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);

        } catch (QueryException  $e) {
            Log::error("Message creation failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Message could not be saved',
                'error' => 'Database error',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception  $e) {
            Log::error("Unexpected error in message creation: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Unexpected error occured',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
      
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
