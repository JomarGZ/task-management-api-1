<?php

namespace App\Http\Requests\api\v1\Chats;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class StoreParticipantRequest extends FormRequest
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
            'participant_ids' => [
                'required', 
                'array'
                ],
            'participant_ids.*' => [
                'required', 
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    $channelId = $this->route('channel')->id;
                    $exists = DB::table('channel_participants')
                        ->where('channel_id', $channelId)
                        ->where('user_id', $value)
                        ->exists();
                    if ($exists) {
                        $fail('The user is already a participant of this channel.');
                    }
                }
            ]
        ];
    }
}
