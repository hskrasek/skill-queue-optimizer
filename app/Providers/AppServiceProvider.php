<?php

namespace App\Providers;

use App\ESI\Attributes;
use App\ESI\Http\Middleware;
use App\ESI\Implant;
use App\ESI\Skill;
use App\ESI\SkillQueueItem;
use App\Models\Type;
use App\Models\User;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Carbon\CarbonInterval;
use Illuminate\Http\Client\PendingRequest;
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
        CarbonInterval::enableFloatSetters();

        if (!$this->app->environment('production')) {
            $this->app->register(IdeHelperServiceProvider::class);
        }

        Collection::macro('queueTimeInterval', function (Attributes $attributes): CarbonInterval {
            return CarbonInterval::seconds(
                $this->map(fn(SkillQueueItem $skillQueueItem): CarbonInterval => $skillQueueItem->trainingTime($attributes))
                    ->sum(fn(CarbonInterval $interval): float => $interval->totalSeconds)
            )->cascade();
        });

        Collection::macro('queueTime', function (Attributes $attributes): string {
            return $this->queueTimeInterval($attributes)->forHumans(
                ['short' => false, 'parts' => 3, 'join' => true, 'skip' => ['year', 'month', 'weeks',]]
            );
        });

        Http::macro('esi', fn(string $token, string $refreshToken): PendingRequest => Http::withHeaders(
            [
                'User-Agent' => 'Eve-Online-CLI-App',
                'Authorization' => 'Bearer ' . $token,
            ]
        )->baseUrl('https://esi.evetech.net/latest/')
            ->withMiddleware(Middleware::refreshToken($token, $refreshToken)));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
