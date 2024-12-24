<?php

namespace App\Http\Requests\api\v1\auth;

use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
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
            'name'      => ['required', 'string','max:255'],
            'email'     => ['required', 'email','max:255', 'unique:users,email'],
            'password'  => ['required', 'string','max:255', 'confirmed'],
        ];
    }

      /**
     * Define the body parameters for the registration request.
     * 
     * @return array
     */
    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'The name of the user.',
                'example' => 'John Doe'
            ],
            'email' => [
                'description' => 'The email address of the user.',
                'example' => 'johndoe@example.com'
            ],
            'password' => [
                'description' => 'The password for the user account.',
                'example' => 'password123'
            ],
            'password_confirmation' => [
                'description' => 'Confirmation of the user password.',
                'example' => 'password123'
            ],
        ];
    }
}
