<?php

namespace App\Http\Requests\api\v1\TaskComment;

use App\Models\Comment;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaskCommentRequest extends FormRequest
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
        ];
    }

    public function bodyParameters()
    {
        return [
            'content' => [
                'description' => 'The content of the comment',
                'example' => 'This is a new comment'
            ],
        ];
    }
}
