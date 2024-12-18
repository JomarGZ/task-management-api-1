<?php

namespace App\Http\Requests\api\v1\TenantMembers;

use App\Models\User;
use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('create', User::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'role' => ['required', Rule::in(Role::cases())],
        ];
    }

    public function messages()
    {
        $roles = implode(',', array_column(Role::cases(), 'value'));
        return [
            'role.in' => "The selected role is invalid. The valid roles are: $roles",
        ];
    }
}
