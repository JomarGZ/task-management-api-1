<?php
namespace App\Services\v1\Messages;

use App\Http\Requests\api\v1\Chats\StoreGeneralMessageRequest;
use App\Http\Requests\api\v1\Chats\UpdateGeneralMessageRequest;
use App\Interfaces\MessageHandlerInterface;
use App\Models\Channel;
use App\Models\Message;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

class GeneralMessageHandler implements MessageHandlerInterface 
{
    public function handle(Channel $channel, array $data): Message
    {
        return $channel->messages()->create([
            'user_id' => auth()->id(),
            'parent_id' => $data['parent_id'],
            'content' => $data['content'],
        ]);
    }
    
    public function validateStore(FormRequest $request): array
    {
        $generalRequest = StoreGeneralMessageRequest::createFrom($request)
            ->setContainer(app())
            ->setRedirector(app(Redirector::class));
        
        $generalRequest->validateResolved();
        
        return $generalRequest->validated();

    }
    public function validateUpdate(FormRequest $request): array
    {
        $generalRequest = UpdateGeneralMessageRequest::createFrom($request)
            ->setContainer(app())
            ->setRedirector(app(Redirector::class));
        
        $generalRequest->validateResolved();
        
        return $generalRequest->validated();

    }

    public function resolveChannel(Request $request): Channel
    {
        return Channel::general();
    }

}