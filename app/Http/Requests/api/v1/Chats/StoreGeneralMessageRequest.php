<?php

namespace App\Http\Requests\api\v1\Chats;

use App\Models\Channel;
use Illuminate\Foundation\Http\FormRequest;

class StoreGeneralMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Channel::isGeneralChannelMember();
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
        ];
    }
}
