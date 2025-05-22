<?php

namespace App\Http\Requests\api\v1\Profile;

use App\Enums\PositionEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
            'name' => 'nullable|string|max:255',
            'position' => ['nullable', Rule::in(PositionEnum::cases())],
            'email' => 'nullable|email|unique:users,email,' . auth()->id(),
        ];
    }
}
