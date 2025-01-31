<?php

namespace App\Enums;

enum Role: string
{
    case ADMIN = 'admin';
    case MEMBER = 'member';
    case PROJECT_MANAGER = 'project manager';
    case TEAM_LEAD = 'team lead';

    
    case JUNIOR_BACKEND_WEB = 'Junior Backend Developer';
    case SENIOR_BACKEND_WEB = 'Senior Backend Developer';
    case JUNIOR_FRONTEND_WEB = 'Junior Frontend Developer';
    case SENIOR_FRONTEND_WEB = 'Senior Frontend Developer';

    case JUNIOR_ANDROID = 'Junior Android Developer';
    case SENIOR_ANDROID = 'Senior Android Developer';
    case JUNIOR_IOS = 'Junior IOS Developer';
    case SENIOR_IOS = 'Senior IOS Developer';
    
    case QA_TESTER = 'QA Tester';
    case UI_UX_DESIGNER = 'UI/UX Designer';

}
