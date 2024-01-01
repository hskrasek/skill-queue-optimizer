<?php

declare(strict_types=1);

namespace App\ESI;

use App\Models\Type;
use App\Models\TypeAttribute;
use Illuminate\Support\Str;
use Livewire\Wireable;

final readonly class Implant implements Wireable
{
    private function __construct(
        public string $name,
        public string $attribute,
        public int $value,
        public int $slot,
    ) {
    }

    public static function make(Type $type): self
    {
        return new self(
            name: $type->typeName,
            attribute: Str::of(
                $type->attributes->first(function (TypeAttribute $typeAttribute): bool {
                    return in_array(
                            $typeAttribute->attributeID,
                            [175, 176, 177, 178, 179],
                            true
                        ) && $typeAttribute->valueFloat > 0;
                })?->attribute->attributeName
            )->explode(' ')->first(),
            value: (int)$type->attributes->first(function (TypeAttribute $typeAttribute): bool {
                return in_array(
                        $typeAttribute->attributeID,
                        [175, 176, 177, 178, 179],
                        true
                    ) && $typeAttribute->valueFloat > 0;
            })?->valueFloat,
            slot: (int)$type->attributes->first(function (TypeAttribute $typeAttribute): bool {
                return $typeAttribute->attributeID === 331;
            })?->valueFloat,
        );
    }

    #[\Override]
    public function toLivewire(): array
    {
        return [
            'name' => $this->name,
            'attribute' => $this->attribute,
            'value' => $this->value,
            'slot' => $this->slot,
        ];
    }

    #[\Override]
    public static function fromLivewire($value): Implant
    {
        return new self(
            name: $value['name'],
            attribute: $value['attribute'],
            value: $value['value'],
            slot: $value['slot'],
        );
    }
}
