<?php

declare(strict_types=1);

namespace App\ESI\Http;

use App\Models\Models\Models\Models\ESI\Auth\Token;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Utils;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;
use Psr\Http\Message\RequestInterface;

final class Middleware
{
    /**
     * @phpstan-return  callable<RequestInterface>
     */
    public static function refreshToken(string $token, string $refreshToken): callable
    {
        return static function (callable $handler) use ($token, $refreshToken): callable {
            return static function (RequestInterface $request, array $options) use ($handler, $token, $refreshToken) {
                $response = $handler($request, $options);

                if ($response instanceof PromiseInterface) {
                    /** @var Response $originalResponse */
                    $originalResponse = $response->wait();

                    if ($originalResponse->getStatusCode() === 200) {
                        return $response;
                    }

                    $authorization = 'Basic ' . base64_encode(
                            config('services.eveonline.client_id') . ':' . config(
                                'services.eveonline.client_secret'
                            )
                        );

                    $response = Http::withOptions([
                        'headers' => [
                            'Authorization' => $authorization,
                        ],
                        'base_uri' => 'https://login.eveonline.com/v2/oauth/token/',
                    ])->asForm()->post('', [
                        'grant_type' => 'refresh_token',
                        'refresh_token' => $refreshToken,
                    ]);

                    auth()->user()->update([
                        'esi_token' => $response->json()['access_token'],
                        'esi_refresh_token' => $response->json()['refresh_token'],
                    ]);
                }

                return $handler($request->withHeader('Authorization', 'Bearer ' . $response->json()['access_token']), $options);
            };
        };
    }
}
