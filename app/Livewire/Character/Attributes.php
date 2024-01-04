<?php

namespace App\Livewire\Character;

use App\ESI\Attributes as CharacterAttributes;
use App\ESI\Character;
use App\ESI\Implant;
use App\ESI\SkillQueueItem;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Attributes extends Component
{
    public CharacterAttributes $characterAttributes;

    /**
     * @var Collection<int, SkillQueueItem>
     */
    #[Locked]
    public Collection $skillQueue;

    /**
     * @var Collection<int, Implant>
     */
    #[Locked]
    public Collection $implants;

    public array $optimal = [];

    public array $difference = [];

    public function mount(Character $character, Collection $skillQueue)
    {
        $this->characterAttributes = $character->attributes;
        $this->skillQueue = $skillQueue;
        $this->implants = $character->implants;
    }

    public function optimize()
    {
//        var skillPoints: [Key: Int] = [:]
//            for item in trainingQueue.queue {
//                let sp = item.finishSP - item.startSP
//                let key = Key(primary: item.skill.primaryAttributeID, secondary: item.skill.secondaryAttributeID)
//                skillPoints[key, default: 0] += sp
//            }
//
//            let basePoints = 17
//            let bonusPoints = 14
//            let maxPoints = 27
//            let totalMaxPoints = basePoints * 5 + bonusPoints
//            var minTrainingTime = TimeInterval.greatestFiniteMagnitude
//
//            var optimal = Pilot.Attributes.default
//
//            for intelligence in basePoints...maxPoints {
//        for memory in basePoints...maxPoints {
//            for perception in basePoints...maxPoints {
//                guard intelligence + memory + perception < totalMaxPoints - basePoints * 2 else {break}
//                        for willpower in basePoints...maxPoints {
//                    guard intelligence + memory + perception + willpower < totalMaxPoints - basePoints else {break}
//                            let charisma = totalMaxPoints - (intelligence + memory + perception + willpower)
//                            guard charisma <= maxPoints else {continue}
//
//                            let attributes = Pilot.Attributes(intelligence: intelligence, memory: memory, perception: perception, willpower: willpower, charisma: charisma)
//
//                            let trainingTime = skillPoints.reduce(0) { (t, i) -> TimeInterval in
//                                let primary = attributes[i.key.primary]
//                                let secondary = attributes[i.key.secondary]
//                                return t + TimeInterval(i.value) / (TimeInterval(primary) + TimeInterval(secondary) / 2)
//                            }
//
//                            if trainingTime < minTrainingTime {
//                                minTrainingTime = trainingTime
//                                optimal = attributes
//                            }
//                        }
//                    }
//                }
//            }

        $skillPoints = [];

        foreach ($this->skillQueue as $skillQueueItem) {
            if (!array_key_exists(
                $skillQueueItem->skill->primaryAttribute . '|' . $skillQueueItem->skill->secondaryAttribute,
                $skillPoints
            )) {
                $skillPoints[$skillQueueItem->skill->primaryAttribute . '|' . $skillQueueItem->skill->secondaryAttribute] = 0;
            }

            $skillPoints[$skillQueueItem->skill->primaryAttribute . '|' . $skillQueueItem->skill->secondaryAttribute] += $skillQueueItem->finishSp - $skillQueueItem->startSp;
        }

        // TODO: Move this to the Attributes class... the non-Livewire one
        $basePoints = 17;
        $bonusPoints = 14;
        $maxPoints = 27;
        $totalMaxPoints = $basePoints * 5 + $bonusPoints;
        $minTrainingTime = PHP_FLOAT_MAX;

        $optimal = [
            'Charisma' => 0,
            'Intelligence' => 0,
            'Memory' => 0,
            'Perception' => 0,
            'Willpower' => 0,
        ];

        for ($intelligence = $basePoints; $intelligence <= $maxPoints; $intelligence++) {
            for ($memory = $basePoints; $memory <= $maxPoints; $memory++) {
                for ($perception = $basePoints; $perception <= $maxPoints; $perception++) {
                    if ($intelligence + $memory + $perception >= $totalMaxPoints - $basePoints * 2) {
                        break;
                    }

                    for ($willpower = $basePoints; $willpower <= $maxPoints; $willpower++) {
                        if ($intelligence + $memory + $perception + $willpower >= $totalMaxPoints - $basePoints) {
                            break;
                        }

                        $charisma = $totalMaxPoints - ($intelligence + $memory + $perception + $willpower);

                        if ($charisma > $maxPoints) {
                            continue;
                        }

                        $attributes = [
                            'Intelligence' => $intelligence,
                            'Memory' => $memory,
                            'Perception' => $perception,
                            'Willpower' => $willpower,
                            'Charisma' => $charisma
                        ];

                        $trainingTime = array_reduce(
                            array_keys($skillPoints),
                            function ($t, $key) use ($attributes, $skillPoints) {
                                [$primaryKey, $secondaryKey] = explode('|', $key);

                                $primary = $attributes[$primaryKey];
                                $secondary = $attributes[$secondaryKey];

                                return $t + $skillPoints[$key] / ($primary + $secondary / 2);
                            },
                            0
                        );

                        if ($trainingTime < $minTrainingTime) {
                            $minTrainingTime = $trainingTime;
                            $optimal = $attributes;
                        }
                    }
                }
            }
        }

        $attributes = [];

        foreach ($this->characterAttributes->values() as $key => $value) {
            $attributes[ucfirst($key->value)] = $value;
        }

        $optimal = $this->adjustOptimalBasedOnCharacter($this->implants, $optimal);

        $this->optimal = $optimal;

        $this->difference = [];

        foreach ($optimal as $key => $value) {
            $this->difference[$key] = $value - $attributes[$key];
        }

        $this->dispatch(event: 'character.attributes.optimized', attributes: $optimal)
            ->to(SkillQueue::class);

        return $this;
    }

    public function render(): View
    {
        return view('components.character.attributes', [
            'attributes' => $this->characterAttributes,
            'optimal' => $this->optimal,
            'difference' => $this->difference,
        ]);
    }

    /**
     * @param Collection<int, Implant> $implants
     * @param array<string, int> $optimal
     *
     * @return array<string, int>
     */
    private function adjustOptimalBasedOnCharacter(Collection $implants, array $optimal): array
    {
        $implantBoosts = $implants
            ->filter(fn(Implant $implant): bool => $implant->value > 0)
            ->mapWithKeys(fn(Implant $implant): array => [
                $implant->attribute => $implant->value,
            ])->all();

        $optimal['Intelligence'] += $implantBoosts['Intelligence'] ?? 0;
        $optimal['Memory'] += $implantBoosts['Memory'] ?? 0;
        $optimal['Charisma'] += $implantBoosts['Charisma'] ?? 0;
        $optimal['Perception'] += $implantBoosts['Perception'] ?? 0;
        $optimal['Willpower'] += $implantBoosts['Willpower'] ?? 0;

        return $optimal;
    }
}
