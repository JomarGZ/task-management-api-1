<?php
namespace App\Services\v1\Messages;

use App\Http\Requests\api\v1\Chats\GeneralMessageRequest;
use App\Interfaces\MessageHandlerInterface;
use App\Models\Channel;
use App\Models\Message;
use Illuminate\Http\Request;

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
    
    public function validate(GeneralMessageRequest $request): array
    {
        return $request->validated();
    }
}