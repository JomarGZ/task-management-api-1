<?php

namespace App\Http\Requests\api\v1\TenantMembers;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterTenantMemberRequest extends FormRequest
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
            'role' => ['sometimes', Rule::in(Role::cases())],
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
