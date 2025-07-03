<?php

namespace App\Http\Requests\api\v1\Chats;

use App\Models\Channel;
use Illuminate\Foundation\Http\FormRequest;

class StoreGroupMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Channel::isGroupChannelMember($this->channel_id);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'content' => 'required|string|max:1000',
            'channel_id' => ['required', 'exists:channels,id'],
            'parent_id' => 'nullable|exists:messages,id'
        ];
    }
}
