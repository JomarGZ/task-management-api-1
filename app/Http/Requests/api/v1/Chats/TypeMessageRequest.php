<?php

namespace App\Http\Requests\api\v1\Chats;

use App\Enums\ChatTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TypeMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'message_type' => ['required', Rule::in(ChatTypeEnum::cases())],
            'channel_id' => ['required', 'exists:channels,id']
        ];
    }
}
