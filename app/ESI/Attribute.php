<?php

declare(strict_types=1);

namespace App\ESI;

use App\ESI\Attributes\Slot;
use ArchTech\Enums\Meta\Meta;
use ArchTech\Enums\Metadata;
use Illuminate\Support\Arr;

/**
 * @method int slot()
 */
#[Meta(Slot::class)]
enum Attribute: string
{
    use Metadata;

    #[Slot(value: 1)]
    case PERCEPTION = 'perception';

    #[Slot(value: 2)]
    case MEMORY = 'memory';

    #[Slot(value: 3)]
    case WILLPOWER = 'willpower';

    #[Slot(value: 4)]
    case INTELLIGENCE = 'intelligence';

    #[Slot(value: 5)]
    case CHARISMA = 'charisma';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return array_map(
            fn (Attribute $attribute): string => $attribute->value,
            Arr::sort(self::cases(), fn (Attribute $attribute): int => $attribute->slot()),
        );
    }
}
