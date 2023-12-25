<?php

namespace App\Http\Controllers;

use App\Models\Bloodline;
use App\Models\Race;
use App\ESI\{Attributes, Character, Http\Middleware, Implant, Portrait, Skill};
use App\Models\Type;
use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class AppController extends Controller
{
    public function index()
    {
        //TODO: Cache this
        //TODO: Move this to a service
        $responses = Http::pool(fn($pool) => [
            $pool->as('character')
                ->baseUrl('https://esi.evetech.net/latest/')
                ->withHeaders([
                    'User-Agent' => 'Eve-Online-CLI-App',
                    'Authorization' => 'Bearer ' . auth()->user()->esi_token,
                ])
                ->withMiddleware(Middleware::refreshToken(auth()->user()->esi_token, auth()->user()->esi_refresh_token))
                ->get('characters/' . auth()->user()->character_id),
            $pool->as('portrait')
                ->baseUrl('https://esi.evetech.net/latest/')
                ->withHeaders([
                    'User-Agent' => 'Eve-Online-CLI-App',
                    'Authorization' => 'Bearer ' . auth()->user()->esi_token,
                ])
                ->withMiddleware(Middleware::refreshToken(auth()->user()->esi_token, auth()->user()->esi_refresh_token))
                ->get('characters/' . auth()->user()->character_id . '/portrait/'),
            $pool->as('attributes')
                ->baseUrl('https://esi.evetech.net/latest/')
                ->withHeaders([
                    'User-Agent' => 'Eve-Online-CLI-App',
                    'Authorization' => 'Bearer ' . auth()->user()->esi_token,
                ])
                ->withMiddleware(Middleware::refreshToken(auth()->user()->esi_token, auth()->user()->esi_refresh_token))
                ->get('characters/' . auth()->user()->character_id . '/attributes'),
            $pool->as('implants')
                ->baseUrl('https://esi.evetech.net/latest/')
                ->withHeaders([
                    'User-Agent' => 'Eve-Online-CLI-App',
                    'Authorization' => 'Bearer ' . auth()->user()->esi_token,
                ])
                ->withMiddleware(Middleware::refreshToken(auth()->user()->esi_token, auth()->user()->esi_refresh_token))
                ->get('characters/' . auth()->user()->character_id . '/implants'),
            $pool->as('queue')
                ->baseUrl('https://esi.evetech.net/latest/')
                ->withHeaders([
                    'User-Agent' => 'Eve-Online-CLI-App',
                    'Authorization' => 'Bearer ' . auth()->user()->esi_token,
                ])
                ->withMiddleware(Middleware::refreshToken(auth()->user()->esi_token, auth()->user()->esi_refresh_token))
                ->get('characters/' . auth()->user()->character_id . '/skillqueue'),
        ]);

        $character = $responses['character']->json();

        $character = new Character(
            name: $character['name'],
            description: $character['description'] ?? '',
            gender: $character['gender'],
            race: Race::find($character['race_id']),
            bloodline: Bloodline::find($character['bloodline_id']),
            birthday: CarbonImmutable::parse($character['birthday']),
            portrait: new Portrait(
                x64: $responses['portrait']->json()['px64x64'],
                x128: $responses['portrait']->json()['px128x128'],
                x256: $responses['portrait']->json()['px256x256'],
                x512: $responses['portrait']->json()['px512x512'],
            ),
            securityStatus: $character['security_status'],
            attributes: Attributes::make($responses['attributes']->json()),
            implants: Type::with(['group', 'attributes',])
                ->whereIn(
                    'typeID',
                    $responses['implants']->json()
                )
                ->get()
                ->map(fn(Type $type) => Implant::make($type))
        );

        $skillQueue = Type::with(['group', 'attributes',])
            ->whereIn('typeID', Arr::pluck($responses['queue']->json(), 'skill_id'))
            ->get()
            ->keyBy('typeID')
            ->toBase()
            ->sortBy(fn(Type $type) => array_search($type->typeID, Arr::pluck($responses['queue']->json(), 'skill_id')))
            ->zipByKey(collect($responses['queue']->json())->keyBy('skill_id'))
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

        $optimal = $this->adjustOptimalBasedOnCharacter(
            $character->implants,
            $optimal,
        );

        //TODO: Update to use Attributes object
//        $currentSkillTime = $skillQueue->map(fn(Skill $skill) => $skill->trainingTime($attributes->all()))
//            ->sum();
//        $optimalSkillTime = $skillQueue->map(fn(Skill $skill) => $skill->trainingTime($optimal))
//            ->sum();
//
//        $savedTime = CarbonInterval::seconds(
//            $optimalSkillTime - $currentSkillTime
//        )->cascade()->forHumans(['short' => false, 'parts' => 3, 'join' => true, 'skip' => ['year', 'month', 'weeks',]]);
//
//        $finishedTime = CarbonInterval::seconds(
//            $currentSkillTime
//        )->cascade()->forHumans(['short' => false, 'parts' => 3, 'join' => true, 'skip' => ['year', 'month', 'weeks',]]);
//
//        $optimalFinishTime = CarbonInterval::seconds(
//            $optimalSkillTime
//        )->cascade()->forHumans(['short' => false, 'parts' => 3, 'join' => true, 'skip' => ['year', 'month', 'weeks',]]);

        return view(
            view: 'app',
            data: [
                'character' => $character,
                'skillQueue' => $skillQueue,
                'optimal' => $optimal,
            ],
        );
    }

    public function optimize()
    {
        $responses = Http::pool(fn($pool) => [
            $pool->as('character')
                ->baseUrl('https://esi.evetech.net/latest/')
                ->withHeaders([
                    'User-Agent' => 'Eve-Online-CLI-App',
                    'Authorization' => 'Bearer ' . auth()->user()->esi_token,
                ])
                ->withMiddleware(Middleware::refreshToken(auth()->user()->esi_token, auth()->user()->esi_refresh_token))
                ->get('characters/' . auth()->user()->character_id),
            $pool->as('portrait')
                ->baseUrl('https://esi.evetech.net/latest/')
                ->withHeaders([
                    'User-Agent' => 'Eve-Online-CLI-App',
                    'Authorization' => 'Bearer ' . auth()->user()->esi_token,
                ])
                ->withMiddleware(Middleware::refreshToken(auth()->user()->esi_token, auth()->user()->esi_refresh_token))
                ->get('characters/' . auth()->user()->character_id . '/portrait/'),
            $pool->as('attributes')
                ->baseUrl('https://esi.evetech.net/latest/')
                ->withHeaders([
                    'User-Agent' => 'Eve-Online-CLI-App',
                    'Authorization' => 'Bearer ' . auth()->user()->esi_token,
                ])
                ->withMiddleware(Middleware::refreshToken(auth()->user()->esi_token, auth()->user()->esi_refresh_token))
                ->get('characters/' . auth()->user()->character_id . '/attributes'),
            $pool->as('implants')
                ->baseUrl('https://esi.evetech.net/latest/')
                ->withHeaders([
                    'User-Agent' => 'Eve-Online-CLI-App',
                    'Authorization' => 'Bearer ' . auth()->user()->esi_token,
                ])
                ->withMiddleware(Middleware::refreshToken(auth()->user()->esi_token, auth()->user()->esi_refresh_token))
                ->get('characters/' . auth()->user()->character_id . '/implants'),
            $pool->as('queue')
                ->baseUrl('https://esi.evetech.net/latest/')
                ->withHeaders([
                    'User-Agent' => 'Eve-Online-CLI-App',
                    'Authorization' => 'Bearer ' . auth()->user()->esi_token,
                ])
                ->withMiddleware(Middleware::refreshToken(auth()->user()->esi_token, auth()->user()->esi_refresh_token))
                ->get('characters/' . auth()->user()->character_id . '/skillqueue'),
        ]);

        $character = $responses['character']->json();

        $character = new Character(
            name: $character['name'],
            description: $character['description'] ?? '',
            gender: $character['gender'],
            race: Race::find($character['race_id']),
            bloodline: Bloodline::find($character['bloodline_id']),
            birthday: CarbonImmutable::parse($character['birthday']),
            portrait: new Portrait(
                x64: $responses['portrait']->json()['px64x64'],
                x128: $responses['portrait']->json()['px128x128'],
                x256: $responses['portrait']->json()['px256x256'],
                x512: $responses['portrait']->json()['px512x512'],
            ),
            securityStatus: $character['security_status'],
            attributes: Attributes::make($responses['attributes']->json()),
            implants: Type::with(['group', 'attributes',])
                ->whereIn(
                    'typeID',
                    $responses['implants']->json()
                )
                ->get()
                ->map(fn(Type $type) => Implant::make($type))
        );

        $skillQueue = Type::with(['group', 'attributes',])
            ->whereIn('typeID', Arr::pluck($responses['queue']->json(), 'skill_id'))
            ->get()
            ->keyBy('typeID')
            ->toBase()
            ->sortBy(fn(Type $type) => array_search($type->typeID, Arr::pluck($responses['queue']->json(), 'skill_id')))
            ->zipByKey(collect($responses['queue']->json())->keyBy('skill_id'))
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

        $optimal = $this->adjustOptimalBasedOnCharacter(
            $character->implants,
            $optimal,
        );

        return $optimal;
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
