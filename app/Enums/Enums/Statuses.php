<?php

namespace App\Enums\Enums;

enum Statuses: string
{
    case NOT_STARTED = 'not started';
    case TO_DO = 'to do';
    case PLANNING = 'planning';
    case IN_PROGRESS = 'in progress';
    case ON_HOLD = 'on hold';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case REVIEW = 'review';
    case TESTING = 'testing';
    case DEPLOYED = 'deployed';
    case PENDING_APPROVAL = 'pending approval';
    case BACKLOG = 'backlog';
    case BLOCKED = 'blocked';
    case REOPENED = 'reopened';
    case SCHEDULED = 'scheduled';
    case DRAFT = 'draft';
    case OVER_DUE = 'overdue';

    public static function getCompletedStatuses(): array
    {
        return [
            self::COMPLETED->value,
            self::DEPLOYED->value,
            self::REVIEW->value,
            self::TESTING->value,
        ];
    }
    public static function getInProgressStatuses(): array
    {
        return [
            self::IN_PROGRESS->value,
            self::ON_HOLD->value,
            self::BLOCKED->value,
            self::REOPENED->value,
        ];
    }
    public static function getToDoStatuses(): array
    {
        return [
            self::NOT_STARTED->value,
            self::TO_DO->value,
            self::PLANNING->value,
            self::BACKLOG->value,
            self::SCHEDULED->value,
            self::DRAFT->value,
        ];
    }

    public static function getExceptionalStatuses()
    {
        return [
            self::CANCELLED->value,
            self::PENDING_APPROVAL->value,
            self::OVER_DUE->value,
        ];
    }
}
