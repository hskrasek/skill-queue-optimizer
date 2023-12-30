<?php

namespace App\Livewire\Character;

use App\ESI\Attributes as CharacterAttributes;
use App\ESI\Character;
use App\ESI\Http\Middleware;
use App\ESI\Skill;
use App\Models\Type;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use Livewire\Component;

class Attributes extends Component
{
    public CharacterAttributes $characterAttributes;

    public array $optimal = [];

    public array $difference = [];

    public function mount(Character $character)
    {
        $this->characterAttributes = $character->attributes;
    }

    public function optimize()
    {
        $response = Http::baseUrl('https://esi.evetech.net/latest/')
            ->withHeaders([
                'User-Agent' => 'Eve-Online-CLI-App',
                'Authorization' => 'Bearer ' . auth()->user()->esi_token,
            ])
            ->withMiddleware(Middleware::refreshToken(auth()->user()->esi_token, auth()->user()->esi_refresh_token))
            ->get('characters/' . auth()->user()->character_id . '/skillqueue');


        $skillQueue = Type::with(['group', 'attributes',])
            ->whereIn('typeID', Arr::pluck($response->json(), 'skill_id'))
            ->get()
            ->keyBy('typeID')
            ->toBase()
            ->sortBy(fn(Type $type) => array_search($type->typeID, Arr::pluck($response->json(), 'skill_id')))
            ->zipByKey(collect($response->json())->keyBy('skill_id'))
            /** @phpstan-var array{0: Type, 1: array} $type */
            ->map(fn(array $skillQueue) => Skill::make($skillQueue[0], $skillQueue[1]));

        /** @var array<string, int> $skillPointsByAttributes */
        $skillPointsByAttributes = $skillQueue->groupBy(
            fn(Skill $skill) => $skill->primaryAttribute . '|' . $skill->secondaryAttribute
        )->pipe(
            fn(Collection $attributeGroup) => $attributeGroup->map(fn(Collection $skills) => $skills->sum(
                fn(Skill $skill) => $skill->finishSkillPoints - $skill->startSkillPoints
            ))
        )->all();

        // TODO: Move this to an object
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
                            array_keys($skillPointsByAttributes),
                            function ($t, $key) use ($attributes, $skillPointsByAttributes) {
                                [$primaryKey, $secondaryKey] = explode('|', $key);

                                $primary = $attributes[$primaryKey];
                                $secondary = $attributes[$secondaryKey];
                                return $t + $skillPointsByAttributes[$key] / ($primary + $secondary / 2);
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

        foreach ($this->characterAttributes->values() as $key => $value ) {
            $attributes[ucfirst($key->value)] = $value;
        }

        $this->optimal = $optimal;

        $this->difference = [];

        foreach ($optimal as $key => $value) {
            $this->difference[$key] = $value - $attributes[$key];
        }

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
}
