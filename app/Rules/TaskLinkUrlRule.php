<?php

namespace App\Rules;

use App\Enums\TaskLinkTypeEnum;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;
class TaskLinkUrlRule implements ValidationRule
{

    public function __construct(
        protected ?string $type = null
    ){}
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            $fail('The :attribute must be a string.');
        }
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            $fail('The :attribute must be a valid URL.');
        }
        if (!Str::startsWith(strtolower($value), 'https://')) {
            $fail('The :attribute must use HTTPS protocol');
        }
        if ($this->type) {
            $enumType = TaskLinkTypeEnum::tryFrom($this->type);

            if (! $enumType) {
                $fail('Invalid link type specified.');
            }

            $pattern = $enumType->pattern();

            if (!preg_match($pattern, $value)) {
                $fail(sprintf(
                    'The :attribute must be a valid %s URL (e.g %s)',
                    $enumType->value,
                    $this->getExampleUrl($enumType)
                ));
            }
        }
    }
    protected function getExampleUrl(TaskLinkTypeEnum $type): string
    {
        return match($type) {
            TaskLinkTypeEnum::GOOGLE_DOCUMENT => 'https://docs.google.com/document/...',
            TaskLinkTypeEnum::GOOGLE_SLIDE => 'https://docs.google.com/presentation/...',
            TaskLinkTypeEnum::GITHUB_PR_ISSUE => 'https://github.com/owner/repo/pull/123',
            TaskLinkTypeEnum::FIGMA_DESIGN => 'https://www.figma.com/design/...',
        };
    }
}
