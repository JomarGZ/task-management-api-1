<?php

namespace App\Http\Requests\api\v1\Assignments;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssignmentRequest extends FormRequest
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
            'assigneeIds' => [
                'required',
                'array',
                "max:{$this->maxAssignees()}",
                function ($attribute, $value, $fail) {
                    $existingAssignees = $this->route('task')->users()->count();
                    if ($existingAssignees + count($value) > $this->maxAssignees()) {
                        $fail("Total assigned users cannot exceed " . $this->maxAssignees() . " (already has {$existingAssignees}).");
                    }
                }
            ],
            'assigneeIds.*' => ['exists:users,id'],
        ];
    }

    protected function maxAssignees()
    {
        return config('limits.per_item.max_user_per_task');
    }
}
