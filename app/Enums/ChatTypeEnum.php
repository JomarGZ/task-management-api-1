<?php

namespace App\Enums;

enum ChatTypeEnum: string
{
    case DIRECT = 'direct';
    case GROUP = 'group';
    case GENERAL = 'general';
}
