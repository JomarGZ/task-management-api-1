<?php

namespace App\Enums;

enum NotificationType: string
{
    case TASK_ASSIGNMENT = 'task assignment';
    case PROJECT_ASSIGNMENT = 'project assignment';
    case SYSTEM = 'system';
    case TASK_COMMENT = 'task comment';
    case DEFAULT = 'default';
}
