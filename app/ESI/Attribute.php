<?php

declare(strict_types=1);

namespace App\ESI;

enum Attribute: string
{
    case INTELLIGENCE = 'intelligence';
    case MEMORY = 'memory';
    case CHARISMA = 'charisma';
    case PERCEPTION = 'perception';
    case WILLPOWER = 'willpower';

    public static function values(): array
    {
        return array_map(
            fn (Attribute $attribute): string => $attribute->value,
            self::cases()
        );
    }
}
