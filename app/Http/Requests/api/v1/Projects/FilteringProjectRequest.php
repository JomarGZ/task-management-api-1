<?php

namespace App\Http\Requests\api\v1\Projects;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilteringProjectRequest extends FormRequest
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
            'column'    => ['sometimes', Rule::in(['name', 'description', 'created_at'])],
            'direction' => ['sometimes', Rule::in(['asc', 'desc'])]
        ];
    }

    public function messages()
    {
        return [
            'column.in'     => 'The selected column is invalid. the valid columns are: name, description, created_at',
            'direction.in'  => 'The selected direction is invalid. the valid columns are: asc and desc',
        ];
    }
}
