<?php

namespace App\Http\Requests\api\v1\Projects;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return request()->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:255'],
            'description'   => ['required', 'string', 'max:1000'],
            'client_name'   => ['required', 'string', 'max:255'],
            'started_at'    => ['nullable'],
            'ended_at'      => ['nullable'],
            'budget'        => ['nullable', 'numeric', 'max_digits:10'],
            'status'        => ['nullable'],
            'priority'      => ['nullable'],
        ];
    }

       /**
     * Define body parameters for API documentation (if applicable).
     *
     * @return array<string, array<string, mixed>>
     */
    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'The name of the project.',
                'example' => 'Website Redesign',
            ],
            'description' => [
                'description' => 'A brief description of the project.',
                'example' => 'Redesigning the company website to improve user experience.',
            ],
        ];
    }
}
