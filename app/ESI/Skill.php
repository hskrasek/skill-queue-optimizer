<?php

declare(strict_types=1);

namespace App\ESI;

use App\Models\Type;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

final class Skill
{
    private function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly int $level,
        public readonly float $rank,
        public readonly string $category,
        public readonly string $primaryAttribute,
        public readonly string $secondaryAttribute,
        public readonly ?Carbon $startDate,
        public readonly ?Carbon $finishDate,
        public int $startSkillPoints,
        public int $finishSkillPoints,
    ) {
        $this->finishSkillPoints = $this->skillPointsAtLevel($this->level);
        $this->startSkillPoints = min(
            $this->startSkillPoints,
            $this->skillPointsAtLevel($this->level - 1),
            $this->finishSkillPoints
        );
    }

    /**
     * @param array{finish_date: string, finished_level: int, level_end_sp: int, level_start_sp: int, queue_position: int, skill_id: int, start_date: string, training_start_sp: int} $queue
     */
    public static function make(Type $type, array $queue = []): self
    {
        return new self(
            id: $type->typeID,
            name: $type->typeName,
            level: $queue['finished_level'] ?? 0,
            rank: $type->attributes->firstWhere('attributeID', 275)->valueFloat,
            category: $type->group->groupName,
            primaryAttribute: $type->attributes->firstWhere('attributeID', 180)->value?->attributeName,
            secondaryAttribute: $type->attributes->firstWhere('attributeID', 181)->value?->attributeName,
            startDate: $queue['start_date'] ? Carbon::parse($queue['start_date']) : null,
            finishDate: $queue['finish_date'] ? Carbon::parse($queue['finish_date']) : null,
            startSkillPoints: $queue['level_start_sp'] ?? 0,
            finishSkillPoints: $queue['level_end_sp'] ?? 0,
        );
    }

    public function finishesIn(): string
    {
        return $this->startDate?->diffForHumans(
            $this->finishDate,
            [
                'short' => true,
                'syntax' => CarbonInterface::DIFF_ABSOLUTE,
                'parts' => 3,
                'join' => true,
                'skip' => ['month', 'weeks',]
            ]
        ) ?? 'Unknown';
    }

    /**
     * @param array<string, int> $attributes
     * @return float
     */
    public function trainingTime(array $attributes): float
    {
        return (float)(($this->finishSkillPoints - $this->startSkillPoints) /
            $this->skillPointsPerSecond($attributes));
    }

    public function skillPointsPerSecond(array $attributes): float
    {
        return (($attributes[$this->primaryAttribute] ?? $attributes[strtolower($this->primaryAttribute)]) +
            ($attributes[$this->secondaryAttribute] ?? $attributes[strtolower($this->secondaryAttribute)]) / 2) / 60;
    }

    public function skillPointsAtLevel(int $level): int
    {
        if ($level === 0 || $this->rank === 0.0) {
            return 0;
        }

        $sp = pow(2, 2.5 * $level - 2.5) * 250.0 * $this->rank;

        return (int)round($sp);
    }
}
