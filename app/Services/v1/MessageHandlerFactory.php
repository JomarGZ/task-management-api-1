<?php
namespace App\Services\v1;

use App\Enums\ChatTypeEnum;
use App\Interfaces\MessageHandlerInterface;
use App\Services\v1\Messages\DirectMessageHandler;
use App\Services\v1\Messages\GeneralMessageHandler;
use App\Services\v1\Messages\GroupMessageHandler;
class MessageHandlerFactory
{
    protected $handlers = [
        ChatTypeEnum::GENERAL->value => GeneralMessageHandler::class,
        ChatTypeEnum::GROUP->value => GroupMessageHandler::class,
        ChatTypeEnum::DIRECT->value => DirectMessageHandler::class
    ];
    
    public function make(string $type): MessageHandlerInterface
    {
        return app($this->handlers[$type]);
    }
}
