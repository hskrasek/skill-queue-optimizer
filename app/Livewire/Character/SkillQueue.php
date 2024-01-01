<?php

namespace App\Livewire\Character;

use App\ESI\Character;
use App\ESI\Skill;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

class SkillQueue extends Component
{
    public Character $character;

    /** @var Collection<int, Skill>  */
    public Collection $skillQueue;

    public function mount(Character $character, Collection $skillQueue)
    {
        $this->character = $character;
        $this->skillQueue = $skillQueue;
    }

    public function render(): View
    {
        return view('components.character.skill-queue');
    }
}
