<?php

declare(strict_types=1);

namespace App\ESI;

use DateTimeImmutable;
use Livewire\Wireable;
use SVG\Nodes\Shapes\SVGRect;
use SVG\SVG;

final readonly class QueuedSkill implements Wireable
{
    public function __construct(
        public int $skillId,
        public int $finishedLevel,
        public int $queuePosition,
        public int $trainingStartSp,
        public int $levelStartSp,
        public int $levelEndSp,
        public ?DateTimeImmutable $startDate = null,
        public ?DateTimeImmutable $finishDate = null,
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

    public function levelProgress(): SVG
    {
        return with(new SVG(10.75 * 5, 10), function (SVG $svg): SVG {
            $doc = $svg->getDocument();
            $doc->setStyle('border', '1px solid #000000');
            $trainedLevels = $this->finishedLevel - 1;

            for ($i = 0; $i < $trainedLevels; $i++) {
                $doc->addChild(
                    (new SVGRect($i * 10.5, 0, 10, 10))
                        ->setStyle('fill', '#d8d8d8')
                        ->setStyle('stroke', '#000000')
                        ->setAttribute('stoke-width', 0.5)
                        ->setAttribute('stoke-opacity', 0.8)
                );
            }

            // Need to determine if future levels are in the queue
            // If so, we need to show the progress of the current level training + the future levels
            for ($i = $trainedLevels; $i < $this->finishedLevel; $i++) {
                $doc->addChild(
                    (new SVGRect($i * 10.5, 0, 10, 10))
                        ->setStyle('fill', '#2FEFEF')
//                        ->setStyle('stroke', '#000000')
                        ->setAttribute('stoke-width', 0.5)
                        ->setAttribute('stoke-opacity', 0.8)
                );
            }

            return $svg;
        });
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
            'startDate' => $this->startDate?->format('Y-m-d H:i:s'),
            'finishDate' => $this->finishDate?->format('Y-m-d H:i:s'),
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
            startDate: isset($value['startDate']) ? new DateTimeImmutable($value['startDate']) : null,
            finishDate: isset($value['finishDate']) ? new DateTimeImmutable($value['finishDate']) : null,
        );
    }
}
