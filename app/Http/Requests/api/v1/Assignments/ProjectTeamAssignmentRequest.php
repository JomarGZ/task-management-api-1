<?php

namespace App\Http\Requests\api\v1\Assignments;

use Illuminate\Foundation\Http\FormRequest;

class ProjectTeamAssignmentRequest extends FormRequest
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
            'assign_team_members' => ['nullable', 'array'],
            'assign_team_members.*' => ['exists:users,id'],
        ];
    }
}
