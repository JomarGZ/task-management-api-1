<?php

namespace App\Http\Requests\api\v1\TenantMembers;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMemeberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update',$this->route('user'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $tenantMember = $this->route('user');
        return [
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($tenantMember?->id)],
            'role'  => ['required', Rule::in(Role::cases())],
        ];
    }

    public function messages()
    {
        $roles = implode(',', array_column(Role::cases(), 'value'));
        return [
            'role.in' => "The selected role is invalid. The valid roles are: $roles",
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
                'description' => 'The full name of the tenant member.',
                'example' => 'John Doe',
            ],
            'email' => [
                'description' => 'A unique email address for the tenant member.',
                'example' => 'john.doe@example.com',
            ],
            'role' => [
                'description' => 'The role to assign to the tenant member. Valid values: `' . implode('`, `', array_column(Role::cases(), 'value')) . '`.',
                'example' => Role::MEMBER->value,
            ],
        ];
    }
}
