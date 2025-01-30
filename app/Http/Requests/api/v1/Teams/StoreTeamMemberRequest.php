<?php

namespace App\Http\Requests\api\v1\Teams;

use App\Enums\Role;
use App\Models\TeamUser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTeamMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return request()->user()->can('create', TeamUser::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'member_id'     => ['required', 'exists:users,id', 'unique:team_user,member_id'],
            'role'          => ['nullable', Rule::in([Role::TEAM_LEAD->value, Role::MEMBER->value, Role::PROJECT_MANAGER])]
        ];
    }

    public function messages()
    {
        $roles = implode(',', array_column(Role::cases(), 'value'));
        return [
            'member_id.required'    => 'Please select at least one member.',
            'member_id.*.exists'    => 'One or more selected members do not exist in the system.',
            'role.in'               => "The selected role is invalid. The valid role are: $roles"
        ];
    }
    
        /**
     * Define body parameters for Scribe documentation (if applicable).
     *
     * @return array<string, array<string, mixed>>
     */
    public function bodyParameters(): array
    {
        return [
            'member_ids' => [
                'description' => 'An array of user IDs to be added as team members.',
                'example' => [1, 2, 3],
            ],
            'role' => [
                'description' => 'The role to assign to the team members. Valid values: `' . implode('`, `', array_column(Role::cases(), 'value')) . '`.',
                'example' => Role::MEMBER->value,
            ],
        ];
    }

}
