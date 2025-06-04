<?php

namespace App\Enums;

enum ChatTypeEnum: string
{
    case DIRECT = 'direct';
    case group = 'group';
    case GENERAL = 'general';
}
