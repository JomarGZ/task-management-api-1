<?php

namespace App\Http\Requests\api\v1\Chats;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDirectMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('message'));
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
