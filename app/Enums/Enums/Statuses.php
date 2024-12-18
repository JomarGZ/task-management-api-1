<?php

namespace App\Enums\Enums;

enum Statuses: string
{
    case NOT_STARTED = 'not started';
    case IN_PROGRESS = 'in progress';
    case COMPLETED = 'completed';
    case ON_HOLD = 'on hold';
    case BLOCKED = 'blocked';
}
