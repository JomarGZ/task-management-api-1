<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NotEqualSender implements ValidationRule
{
    public function __construct(
            private mixed $senderId
    ) {}
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if((string)$value === (string)$this->senderId){
            $fail('You cannot send a message to yourself');
        }
    }
}
