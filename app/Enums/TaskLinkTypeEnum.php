<?php

namespace App\Enums;

enum TaskLinkTypeEnum: string
{
    case GOOGLE_DOCUMENT = 'google document';
    case GOOGLE_SLIDE = 'google slide';
    case GITHUB_PR_ISSUE = 'github pr/issue';
    case FIGMA_DESIGN = 'figma design';

    public function pattern()
    {
        return match($this) {
        self::GOOGLE_DOCUMENT => '/^https:\/\/docs\.google\.com\/document\/.*/',
        self::GOOGLE_SLIDE => '/^https:\/\/docs\.google\.com\/presentation\/.*/',
        self::GITHUB_PR_ISSUE => '{^https://github\.com/[^/]+/[^/]+/(pull|pulls|issue|issues)/\d+/?$}i',
        self::FIGMA_DESIGN => '/^https:\/\/(www\.)?figma\.com\/(file|design)\/[a-zA-Z0-9_-]+(\/.*)?/'
        };
    }

    public function getPatternForType(string $type)
    {
        return self::tryFrom($type)->pattern();
    }
}
