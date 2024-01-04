<?php

namespace App\Livewire\Character;

use App\ESI\Character;
use App\ESI\Skill;
use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\MapperBuilder;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class SkillQueue extends Component
{
    public Character $character;

    /** @var Collection<int, Skill>  */
    public Collection $skillQueue;

    public \App\ESI\Attributes $optimalAttributes;

    public function mount(Character $character, Collection $skillQueue)
    {
        $this->character = $character;
        $this->skillQueue = $skillQueue;
    }

    /**
     * @param array<string, int> $attributes
     * @return void
     * @throws \CuyZ\Valinor\Mapper\MappingError
     */
    #[On(event: 'character.attributes.optimized')]
    public function calculateOptimalTime(array $attributes)
    {
        $this->optimalAttributes = (new MapperBuilder())
            ->registerConstructor(\App\ESI\Attributes::make(...))
            ->allowPermissiveTypes()
            ->mapper()
            ->map(\App\ESI\Attributes::class, Source::array($attributes)->camelCaseKeys());
    }

    public function render(): View
    {
        return view('components.character.skill-queue');
    }
}
