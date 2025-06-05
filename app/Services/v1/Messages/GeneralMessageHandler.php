<?php
namespace App\Services\v1\Messages;

use App\Http\Requests\api\v1\Chats\StoreGeneralMessageRequest;
use App\Interfaces\MessageHandlerInterface;
use App\Models\Channel;
use App\Models\Message;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Redirect;

class GeneralMessageHandler implements MessageHandlerInterface 
{
    public function handle(Channel $channel, array $data): Message
    {
        // General messages might have special handling
        return $channel->messages()->create([
            'user_id' => auth()->id(),
            'content' => $data['content'],
        ]);
    }
    
    public function validate(FormRequest $request): array
    {
        $generalRequest = StoreGeneralMessageRequest::createFrom($request)
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