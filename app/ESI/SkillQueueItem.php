<?php

declare(strict_types=1);

namespace App\ESI;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Livewire\Wireable;

final readonly class SkillQueueItem implements Wireable
{
    public float $startSp;

    public float $finishSp;

    public function __construct(
        public Skill $skill,
        public QueuedSkill $queuedSkill,
    ) {
        $this->finishSp = $this->queuedSkill->levelEndSp ?? $this->skill->skillPoints(
            $this->queuedSkill->finishedLevel
        );
        $this->startSp = min(
            $this->queuedSkill->trainingStartSp,
            $this->finishSp
        );
    }

    public function skillPoints(): int
    {
        if (
            $this->queuedSkill->startDate !== null
            && $this->queuedSkill->finishDate !== null
            && $this->queuedSkill->trainingStartSp !== null
            && $this->queuedSkill->levelEndSp !== null
            && $this->queuedSkill->finishDate > Carbon::now()
        ) {
            $t = $this->queuedSkill->finishDate->diff($this->queuedSkill->startDate)->s;

            if ($t > 0) {
                $spps = ($this->queuedSkill->levelEndSp - $this->queuedSkill->trainingStartSp) / $t;

                $t = $this->queuedSkill->finishDate->diff(Carbon::now())->s;

                $sp = (int)($t > 0 ? $this->queuedSkill->levelEndSp - $t * $spps : $this->queuedSkill->levelEndSp);

                return max($sp, $this->queuedSkill->trainingStartSp);
            }

            return $this->queuedSkill->levelEndSp;
        }

        return $this->skill->skillPoints($this->queuedSkill->finishedLevel - 1);
    }

    public function skillPointsToLevelUp(): int
    {
        return $this->skill->skillPoints($this->queuedSkill->finishedLevel) - $this->skillPoints();
    }

    public function trainingTimeToLevelUp(Attributes $attributes): CarbonInterval
    {
        $sp = $this->skillPointsToLevelUp();
        $spps = $this->skill->skillPointsPerSecond($attributes);

        $t = (int)($sp / $spps);

        return CarbonInterval::seconds($t);
    }

    public function trainingProgress(): float
    {
        $level = $this->queuedSkill->finishedLevel;

        if ($level > 0) {
            $start = (float)$this->skill->skillPoints($level - 1);
            $end = (float)$this->skill->skillPoints($level);
            $left = (float)$this->skillPointsToLevelUp();
            $progress = (1.0 - $left / ($end - $start));

            return max(0.0, min(1.0, $progress));
        }

        return 0.0;
    }

    public function trainingTime(Attributes $attributes): CarbonInterval
    {
        return CarbonInterval::seconds(
            (float)($this->finishSp - $this->startSp) / $this->skill->skillPointsPerSecond($attributes)
        );
    }

    public function isActive(): bool
    {
        $date = Carbon::now();

        return $this->queuedSkill->startDate !== null
            && $this->queuedSkill->finishDate !== null
            && $this->queuedSkill->finishDate > $date
            && $this->queuedSkill->startDate < $date;
    }

    #[\Override]
    public function toLivewire(): array
    {
        return [
            'skill' => $this->skill,
            'queuedSkill' => $this->queuedSkill,
        ];
    }

    #[\Override]
    public static function fromLivewire($value): SkillQueueItem
    {
        return new self(
            $value['skill'],
            $value['queuedSkill'],
        );
    }
}
