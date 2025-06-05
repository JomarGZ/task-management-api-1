<?php
namespace App\Services\v1\Messages;

use App\Http\Requests\api\v1\Chats\StoreDirectMessageRequest;
use App\Http\Requests\api\v1\Chats\UpdateDirectMessageRequest;
use App\Interfaces\MessageHandlerInterface;
use App\Models\Channel;
use App\Models\Message;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

class DirectMessageHandler implements MessageHandlerInterface 
{
    public function handle(Channel $channel, array $data): Message
    {
        return $channel->messages()->create([
            'user_id' => auth()->id(),
            'content' => $data['content'],
        ]);
    }
    
    public function validateStore(FormRequest $request): array
    {
        $generalRequest = StoreDirectMessageRequest::createFrom($request)
            ->setContainer(app())
            ->setRedirector(app(Redirector::class));
        
        $generalRequest->validateResolved();
        
        return $generalRequest->validated();

    }
    public function validateUpdate(FormRequest $request): array
    {
        $generalRequest = UpdateDirectMessageRequest::createFrom($request)
            ->setContainer(app())
            ->setRedirector(app(Redirector::class));
        
        $generalRequest->validateResolved();
        
        return $generalRequest->validated();

    }

    public function resolveChannel(Request $request): Channel
    {
        return Channel::direct($request->sender_id, $request->recipient_id);
    }

}