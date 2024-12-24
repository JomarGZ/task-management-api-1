<?php

namespace App\Http\Requests\api\v1\Tasks;

use App\Enums\Enums\PriorityLevel;
use App\Enums\Enums\Statuses;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (in_array(request()->method(), ['PUT','PATCH'])) {
            return request()->user()->can('update', $this->route('task'));
        }
        return request()->user()->can('create', $this->route('project'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'             => ['required', 'string', 'max:255'],
            'description'       => ['required', 'string', 'max:500'],
            'assigned_id'       => ['nullable', 'exists:users,id'],
            'priority_level'    => ['nullable', Rule::in(PriorityLevel::cases())],
            'status'            => ['sometimes', Rule::in(Statuses::cases())],
            'deadline_at'       => ['nullable'],
            'started_at'        => ['nullable'],
            'completed_at'      => ['nullable'],
        ];
    }

    public function messages()
    {
        $priorityLevels = implode(',', array_column(PriorityLevel::cases(), 'value'));
        $statuses = implode(',', array_column(Statuses::cases(), 'value'));
        return [
            'priority_level.in' => "The selected priority level is invalid. The valid priority level are: $priorityLevels",
            'status.in'         => "The selected statuses is invalid. The valid statuses are: $statuses"
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
            'title' => [
                'description' => 'The title of the task.',
                'example' => 'Implement authentication',
            ],
            'description' => [
                'description' => 'A brief description of the task.',
                'example' => 'Develop login and registration functionality.',
            ],
            'assigned_id' => [
                'description' => 'The ID of the user assigned to the task.',
                'example' => 5,
            ],
            'priority_level' => [
                'description' => 'The priority level of the task.',
                'example' => 'High',
            ],
            'status' => [
                'description' => 'The current status of the task.',
                'example' => 'In Progress',
            ],
            'deadline_at' => [
                'description' => 'The deadline for completing the task.',
                'example' => '2024-01-15',
            ],
            'started_at' => [
                'description' => 'The start date of the task.',
                'example' => '2024-01-01',
            ],
            'completed_at' => [
                'description' => 'The completion date of the task.',
                'example' => '2024-01-20',
            ],
        ];
    }
}
