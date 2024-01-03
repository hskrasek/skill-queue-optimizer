<?php

declare(strict_types=1);

namespace App\ESI;

use App\Models\Type;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Livewire\Wireable;

final class Skill implements Wireable
{
    private function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly float $rank,
        public readonly string $category,
        public readonly string $primaryAttribute,
        public readonly string $secondaryAttribute,
    ) {}

    public static function make(Type $type): self
    {
        return new self(
            id: $type->typeID,
            name: $type->typeName,
            rank: $type->attributes->firstWhere('attributeID', 275)?->valueFloat,
            category: $type->group->groupName,
            primaryAttribute: $type->attributes->firstWhere('attributeID', 180)?->value?->attributeName,
            secondaryAttribute: $type->attributes->firstWhere('attributeID', 181)?->value?->attributeName,
        );
    }

    public function skillPoints(int $atLevel): int
    {
        if ($atLevel === 0 || $this->rank === 0.0) {
            return 0;
        }

        $sp = pow(2, 2.5 * $atLevel - 2.5) * 250.0 * $this->rank;

        return (int) ceil($sp);
    }

    public function level(int $withSkillPoints): int
    {
        if ($withSkillPoints === 0 || $this->rank === 0.0) {
            return 0;
        }

        $level = (log($withSkillPoints / (250.0 * $this->rank)) / log(2.0) + 2.5) / 2.5;

        return (int) floor($level);
    }

    public function skillPointsPerSecond(Attributes $attributes): float
    {
        $primary = $attributes[$this->primaryAttribute];
        $secondary = $attributes[$this->secondaryAttribute];

        return ($primary + $secondary / 2.0) / 60.0;
    }

    #[\Override]
    public function toLivewire(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'rank' => $this->rank,
            'category' => $this->category,
            'primaryAttribute' => $this->primaryAttribute,
            'secondaryAttribute' => $this->secondaryAttribute,
        ];
    }

    #[\Override]
    public static function fromLivewire($value): Skill
    {
        return new self(
            id: $value['id'],
            name: $value['name'],
            rank: $value['rank'],
            category: $value['category'],
            primaryAttribute: $value['primaryAttribute'],
            secondaryAttribute: $value['secondaryAttribute'],
        );
    }
}
