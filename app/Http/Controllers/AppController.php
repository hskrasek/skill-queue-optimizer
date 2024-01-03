<?php

namespace App\Http\Controllers;

use App\Models\Bloodline;
use App\Models\Race;
use App\ESI\{Attributes, Character, Http\Middleware, Implant, Portrait, QueuedSkill, Skill, SkillQueueItem};
use App\Models\Type;
use Carbon\CarbonImmutable;
use CuyZ\Valinor\Mapper\MappingError;
use CuyZ\Valinor\Mapper\Source\JsonSource;
use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\MapperBuilder;
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

        try {
            $objects = (new MapperBuilder())
                ->mapper()
                ->map(
                    'array<' . QueuedSkill::class . '>',
                    Source::array($responses['queue']->json())
                        ->camelCaseKeys(),
                );
        } catch (MappingError $e) {
            // TODO: Handle this better
            dd($e);
        }

        /** @var Collection<int, SkillQueueItem> $skillQueue */
        $skillQueue = Collection::make($objects)
            ->map(fn(QueuedSkill $queuedSkill) => new SkillQueueItem(
                skill: Skill::make(Type::with(['group', 'attributes',])
                    ->find($queuedSkill->skillId)),
                queuedSkill: $queuedSkill,
            ));

        return view(
            view: 'app',
            data: [
                'character' => $character,
                'skillQueue' => $skillQueue,
            ],
        );
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
