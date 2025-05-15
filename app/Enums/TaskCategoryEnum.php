<?php

namespace App\Enums;

enum TaskCategoryEnum: string
{
    case UI_UX_DESIGN = 'ui/ux design';
    case BACKEND_DEVELOPMENT = 'backend development';
    case FRONTEND_DEVELOPMENT = 'frontend development';
    case FULLSTACK_DEVELOPMENT = 'fullstack development';
    case IOS_DEVELOPMENT = 'ios development';
    case ANDROID_DEVELOPMENT = 'android development';
    case DEVOPS = 'devops';
    case QUALITY_ASSURANCE = 'quality assurance';
    case DATABASE_DESIGN = 'database design';
    case PERFORMANCE_OPTIMIZATION = 'performance optimization';
    case TECHNICAL_DOCUMENTATION = 'technical documentation';
    case OTHER = 'other';

}
