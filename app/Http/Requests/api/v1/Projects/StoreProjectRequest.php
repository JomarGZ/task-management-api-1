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
        if (in_array(request()->method(), ['PUT', 'PATCH'])) {
            return request()->user()->can('update', request()->route('project'));
        }
        return request()->user()->can('create', Project::class);
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
            'description'   => ['required', 'string', 'max:500'],
            'team_id'       => ['sometimes', 'exists:teams,id'],
            'project_manager' => ['sometimes', 'exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'team_id.exists' => 'The selected team does not exist in the organization',
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
            'team_id' => [
                'description' => 'The ID of the team assigned to the project. This field is optional.',
                'example' => 3,
            ],
        ];
    }
}
