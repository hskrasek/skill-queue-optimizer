<?php

namespace App\Providers;

use App\ESI\Http\Middleware;
use App\ESI\Implant;
use App\ESI\Skill;
use App\Models\Type;
use App\Models\User;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (!$this->app->environment('production')) {
            $this->app->register(IdeHelperServiceProvider::class);
        }

        Collection::macro('zipByKey', function (Collection $collection): Collection {
            return $this->mapWithKeys(function ($item, $key) use ($collection) {
                return [$key => [$item, $collection->get($key)]];
            });
        });

        Http::macro('esi', fn(string $token, string $refreshToken): PendingRequest => Http::withHeaders(
            [
                'User-Agent' => 'Eve-Online-CLI-App',
                'Authorization' => 'Bearer ' . $token,
            ]
        )->baseUrl('https://esi.evetech.net/latest/')
            ->withMiddleware(Middleware::refreshToken($token, $refreshToken)));

        Http::macro('skillQueue', function (User $user): Collection {
            $skillQueue = Http::esi($user->esi_token)->get(
                'characters/' . $user->character_id . '/skillqueue'
            )->throw()->json();

            /** @var Collection<string, Skill> $skills */
            return Type::with(['group', 'attributes',])
                ->whereIn('typeID', Arr::pluck($skillQueue, 'skill_id'))
                ->get()
                ->keyBy('typeID')
                ->toBase()
                ->sortBy(fn(Type $type) => array_search($type->typeID, Arr::pluck($skillQueue, 'skill_id')))
                ->zipByKey(collect($skillQueue)->keyBy('skill_id'))
                /** @psalm-var array{0: Type, 1: array} $type */
                ->map(fn(array $skillQueue) => Skill::make($skillQueue[0], $skillQueue[1]));
        });

        /** @return Collection<int, Implant> */
        Http::macro('implants', function (User $user): Collection {
            return Type::with(['group', 'attributes',])
                ->whereIn(
                    'typeID',
                    Http::esi($user->esi_token)->get(
                        'characters/' . $user->character_id . '/implants'
                    )->throw()->json()
                )
                ->get()
                ->map(fn(Type $type) => Implant::make($type));
        });

        /** @return Collection<string, int> */
        Http::macro('attributes', function (User $user): Collection {
            return collect(
                Http::esi($user->esi_token)->get(
                    'https://esi.evetech.net/latest/characters/' . $user->character_id . '/attributes'
                )->throw()->json()
            )->only(['intelligence', 'memory', 'charisma', 'perception', 'willpower']);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
