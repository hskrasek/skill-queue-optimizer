<?php

declare(strict_types=1);

namespace App\ESI;

use App\Models\Type;
use App\Models\TypeAttribute;
use Illuminate\Support\Str;

final readonly class Implant
{
    private function __construct(
        public string $name,
        public string $attribute,
        public int $value,
    ) {}

    public static function make(Type $type): self
    {
        return new self(
            name: $type->typeName,
            attribute: Str::of($type->attributes->first(function (TypeAttribute $typeAttribute): bool {
                return in_array($typeAttribute->attributeID, [175, 176, 177, 178, 179], true) && $typeAttribute->valueFloat > 0;
            })?->attribute->attributeName)->explode(' ')->first(),
            value: (int)$type->attributes->first(function (TypeAttribute $typeAttribute): bool {
                return in_array($typeAttribute->attributeID, [175, 176, 177, 178, 179], true) && $typeAttribute->valueFloat > 0;
            })?->valueFloat,
        );
    }
}
