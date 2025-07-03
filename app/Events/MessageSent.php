<?php

namespace App\Events;

use App\Http\Resources\api\v1\Chats\MessageResource;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $channelName;
    public $channelType;

    public function __construct($message)
    {
        $this->message = $message->loadMissing(['channel.participants']);
        $this->channelType = $message->channel->type;
        
        $this->setChannelName();
    }

    protected function setChannelName()
    {
        if ($this->channelType === 'direct') {
            $recipient = $this->message->channel->participants
                ->where('id', '!=', $this->message->user_id)
                ->first();
                
            if (!$recipient) {
                throw new \RuntimeException('No recipient found for direct message');
            }
            
            $this->channelName = 'direct.'.$recipient->id;
        } else {
            $this->channelName = 'channel.'.$this->message->channel_id;
        }
    }

    public function broadcastOn()
    {
        if ($this->channelType === 'direct') {
            return new PrivateChannel($this->channelName);
        }
        
        $channels = [];
        
        foreach ($this->message->channel->participants as $participant) {
            if ($participant->id !== $this->message->user_id) {
                $channels[] = new PrivateChannel('user.'.$participant->id.'.channel.'.$this->message->channel_id);
            }
        }
        
        return $channels;
    }
    
    public function broadcastWith()
    {
          return [
            'message' => (new MessageResource($this->message))->toArray(request()),
            'channel' => [
                'id' => $this->message->channel->id,
                'type' => $this->message->channel->type,
            ],
            'type' => $this->channelType
        ];
    }
    
    public function broadcastAs()
    {
        return 'message.sent';
    }
}
