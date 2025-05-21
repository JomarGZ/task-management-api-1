<?php

namespace App\Http\Requests\api\v1\tasks;

use App\Enums\TaskLinkTypeEnum;
use App\Rules\TaskLinkUrlRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskLinkRequest extends FormRequest
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
            'title' => 'required|string|max:50',
            'type' => ['required', Rule::in(TaskLinkTypeEnum::cases())],
            'url' => [
                'required',
                'url',
                new TaskLinkUrlRule($this->input('type'))
            ],
            'description' => 'required|string|max:100'
        ];
    }
}
