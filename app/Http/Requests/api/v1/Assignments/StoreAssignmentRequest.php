<?php

namespace App\Http\Requests\api\v1\Assignments;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssignmentRequest extends FormRequest
{
    protected $maxAssignees = config('limits.per_item.max_user_per_task');
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
            'assignees' => [
                'required',
                'exists:users,id', 
                'array',
                "max:{$this->maxAssignees}",
                function ($attribute, $value, $fail) {
                    $existingAssignees = $this->route('task')->users()->count();
                    if ($existingAssignees + count($value)) {
                        $fail("Total assign users cannot exceed {$this->maxAssignees} (already has {$existingAssignees}).");
                    }
                }
            ]
        ];
    }
}
