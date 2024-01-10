<?php

namespace App\Livewire;

use Livewire\Component;

class PlanetaryIndustry extends Component
{
    public array $planets = [];

    public function mount()
    {
        $this->planets = \Http::esi(auth()->user()->esi_token, auth()->user()->esi_refresh_token)
            ->get('/characters/' . auth()->user()->character_id . '/planets/')
            ->json();

        $this->planets = collect($this->planets)->map(function ($planet) {
            $planet['colony'] = \Http::esi(auth()->user()->esi_token, auth()->user()->esi_refresh_token)
                ->get('/characters/' . auth()->user()->character_id . '/planets/' . $planet['planet_id'] . '/')
                ->json();
            $planet['planet'] = \App\Models\Planet::find($planet['planet_id']);

            return $planet;
        })->all();
    }

    public function render()
    {
        return view('components.planetary-industry', [
            'planets' => $this->planets,
        ]);
    }
}
