<?php

namespace App\Http\Requests\api\v1\Chats;

use App\Rules\NotEqualSender;
use Illuminate\Foundation\Http\FormRequest;

class StoreDirectMessageRequest extends FormRequest
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
            'content' => 'required|string|max:1000',
            'sender_id' => 'required|exists:users,id',
            'recipient_id' => [
                'required',
                'exists:users,id',
                new NotEqualSender($this->input('sender_id'))
                ]
        ];
    }
}
