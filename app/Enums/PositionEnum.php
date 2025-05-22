<?php

namespace App\Enums;

enum PositionEnum: string
{
    case FRONTEND_WEB_DEVELOPER = 'frontend web developer';
    case BACKEND_WEB_DEVELOPER = 'backend web developer';
    case FULLSTACK_WEB_DEVELOPER = 'fullstack web developer';
    case IOS_DEVELOPER = 'ios developer';
    case ANDROID_DEVELOPER = 'android developer';
    case UI_UX_DESIGNER = 'ui/ux designer';
    case QA_TESTER = 'qa tester';
    case PROJECT_MANAGER = 'project manager';
    case BRIDGE_ENGINEER = 'bridge engineer';
}
