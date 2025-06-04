<?php
namespace App\Services\v1;

use App\Interfaces\MessageHandlerInterface;
use App\Services\v1\Messages\GeneralMessageHandler;

class MessageHandlerFactory
{
    protected $handlers = [
        'general' => GeneralMessageHandler::class
    ];
    
    public function make(string $type): MessageHandlerInterface
    {
        return app($this->handlers[$type]);
    }
}
