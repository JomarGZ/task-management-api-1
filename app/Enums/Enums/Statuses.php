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
    case ARCHIVED = 'archived';
    case REOPENED = 'reopened';
    case SCHEDULED = 'scheduled';
    case DRAFT = 'draft';
    case OVER_DUE = 'overdue';
}
