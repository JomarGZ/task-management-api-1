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
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'project_id' => ['required', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:500'],
            'priority_level' => ['required', Rule::in(PriorityLevel::cases())],
            'status' => ['required', Rule::in(Statuses::cases())],
        ];
    }

    public function messages()
    {
        $priorityLevels = implode(',', array_column(PriorityLevel::cases(), 'value'));
        $statuses = implode(',', array_column(Statuses::cases(), 'value'));
        return [
            'priority_level.in' => "The selected priority level is invalid. The valid priority level are: $priorityLevels",
            'status.in' => "The selected statuses is invalid. The valid statuses are: $statuses"
        ];
    }
}
