<?php
namespace App\Services\v1\Messages;

use App\Http\Requests\api\v1\Chats\StoreGroupMessageRequest;
use App\Http\Requests\api\v1\Chats\UpdateGroupMessageRequest;
use App\Interfaces\MessageHandlerInterface;
use App\Models\Channel;
use App\Models\Message;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

class GroupMessageHandler implements MessageHandlerInterface
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
        $groupRequest = StoreGroupMessageRequest::createFrom($request)
            ->setContainer(app())
            ->setRedirector(app(Redirector::class));
            
        $groupRequest->validateResolved();

        return $groupRequest->validated();
    }
    public function validateUpdate(FormRequest $request): array
    {
        $groupRequest = UpdateGroupMessageRequest::createFrom($request)
            ->setContainer(app())
            ->setRedirector(app(Redirector::class));
            
        $groupRequest->validateResolved();

        return $groupRequest->validated();
    }

    public function resolveChannel(Request $request): Channel
    {
        return Channel::group($request->channel_id);
    }
}