<?php

declare(strict_types=1);

namespace App\ESI;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Arr;

final readonly class Attributes
{
    /**
     * @var \WeakMap<Attribute, int>
     */
    private \WeakMap $attributes;

    private function __construct(
        array $attributes,
        public ?CarbonInterface $cooldownDate = null,
        public ?CarbonInterface $lastRemapDate = null,
        public int $bonusRemaps = 0
    ) {
        $this->attributes = tap(new \WeakMap(), function (\WeakMap $map) use ($attributes): void {
            $attributes = Arr::only($attributes, Attribute::values());

            foreach ($attributes as $attribute => $value) {
                $map[Attribute::from($attribute)] = $value;
            }
        });
    }

    public static function make(array $attributes): self
    {
        return new self(
            $attributes,
            cooldownDate: CarbonImmutable::parse($attributes['accrued_remap_cooldown_date'] ?? null),
            lastRemapDate: CarbonImmutable::parse($attributes['last_remap_date'] ?? null),
            bonusRemaps: $attributes['bonus_remaps'] ?? 0,
        );
    }

    /**
     * @return \Generator<Attribute, int>
     */
    public function values(): \Generator
    {
        foreach ($this->attributes as $attribute => $value) {
            yield $attribute => $value;
        }
    }
}
