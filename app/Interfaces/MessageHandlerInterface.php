<?php

namespace App\Interfaces;

use App\Models\Channel;
use App\Models\Message;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

interface MessageHandlerInterface
{
    public function handle(Channel $channel, array $data): Message;
    public function validateStore(FormRequest $request): array;
    public function validateUpdate(FormRequest $request): array;
    public function resolveChannel(Request $request): Channel;
}
