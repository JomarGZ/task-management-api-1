<?php

namespace App\Enums;

enum Role: string
{
    case ADMIN = 'admin';
    case MEMBER = 'member';
    case PROJECT_MANAGER = 'project manager';
    case TEAM_LEAD = 'team lead';
}
