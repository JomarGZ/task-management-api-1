<?php

namespace App\Http\Requests\api\v1\auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'email'     => ['required', 'email'],
            'password'  => ['required']
        ];
    }

      /**
     * Define the body parameters for the login request.
     * 
     * @return array
     */
    public function bodyParameters(): array
    {
        return [
            'email' => [
                'description' => 'The email address of the user.',
                'example'     => 'johndoe@example.com',
            ],
            'password' => [
                'description' => 'The password for the user account.',
                'example'     => 'password123',
            ],
        ];
    }
}
