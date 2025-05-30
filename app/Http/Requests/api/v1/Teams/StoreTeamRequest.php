<?php

namespace App\Http\Requests\api\v1\Teams;

use App\Models\Team;
use Illuminate\Foundation\Http\FormRequest;

class StoreTeamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (in_array(request()->method(), ['PUT', 'PATCH'])) {
            request()->user()->can('update', $this->route('team'));
        }
        return request()->user()->can('create', Team::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255']
        ];
    }
    
      /**
     * Get custom body parameters for Scribe documentation.
     *
     * @return array
     */
    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'The name of the team. Must be a string with a maximum length of 255 characters.',
                'example' => 'Engineering Team'
            ],
        ];
    }
}
