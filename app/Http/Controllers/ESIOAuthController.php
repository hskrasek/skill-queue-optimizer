<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Laravel\Socialite\Facades\Socialite;

class ESIOAuthController extends Controller
{
    public function home(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect('/app');
        }

        return view('home');
    }

    public function ssoRedirect(): RedirectResponse
    {
        return Socialite::driver('eveonline')
            ->scopes(config('services.eveonline.scopes'))
            ->redirect();
    }

    public function oauthCallback(Request $request): RedirectResponse
    {
        /** @var \SocialiteProviders\Manager\OAuth2\User $esiCharacter */
        $esiCharacter = Socialite::driver('eveonline')->user();

        $user = User::updateOrCreate([
            'character_owner_hash' => $esiCharacter->character_owner_hash,
            'character_id' => $esiCharacter->character_id,
        ], [
            'esi_token' => $esiCharacter->token,
            'esi_refresh_token' => $esiCharacter->refreshToken,
        ]);

        Auth::login($user);

        return redirect('/app');
    }

    public function logout()
    {
        Auth::logout();

        session()->invalidate();

        return redirect('/');
    }
}
