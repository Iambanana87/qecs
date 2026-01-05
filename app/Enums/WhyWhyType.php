<?php

namespace App\Enums;

enum WhyWhyType: string
{
    case HAPPEN = 'HAPPEN';
    case DETECTION = 'DETECTION';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}