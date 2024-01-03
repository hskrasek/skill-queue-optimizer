<?php

declare(strict_types=1);

namespace App\ESI;

use DateTimeImmutable;
use Livewire\Wireable;

final readonly class QueuedSkill implements Wireable
{
    public function __construct(
        public int $skillId,
        public int $finishedLevel,
        public int $queuePosition,
        public int $trainingStartSp,
        public int $levelStartSp,
        public int $levelEndSp,
        public DateTimeImmutable $startDate,
        public DateTimeImmutable $finishDate,
    ) {
    }

    public function finishedLevel(): string
    {
        return match ($this->finishedLevel) {
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            default => '',
        };
    }

    #[\Override]
    public function toLivewire(): array
    {
        return [
            'skillId' => $this->skillId,
            'finishedLevel' => $this->finishedLevel,
            'queuePosition' => $this->queuePosition,
            'trainingStartSp' => $this->trainingStartSp,
            'levelStartSp' => $this->levelStartSp,
            'levelEndSp' => $this->levelEndSp,
            'startDate' => $this->startDate->format('Y-m-d H:i:s'),
            'finishDate' => $this->finishDate->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * @throws \Exception
     */
    #[\Override]
    public static function fromLivewire($value): QueuedSkill
    {
        return new self(
            skillId: $value['skillId'],
            finishedLevel: $value['finishedLevel'],
            queuePosition: $value['queuePosition'],
            trainingStartSp: $value['trainingStartSp'],
            levelStartSp: $value['levelStartSp'],
            levelEndSp: $value['levelEndSp'],
            startDate: new DateTimeImmutable($value['startDate']),
            finishDate: new DateTimeImmutable($value['finishDate']),
        );
    }
}
