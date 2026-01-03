<?php

namespace App\Enums;

enum FiveMType: string
{
    case MAN = 'MAN';
    case MACHINE = 'MACHINE';
    case METHOD = 'METHOD';
    case MATERIAL = 'MATERIAL';
    case ENVIRONMENT = 'ENVIRONMENT';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
