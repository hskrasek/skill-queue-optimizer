<?php

declare(strict_types=1);

namespace App\ESI;

use App\Models\Bloodline;
use App\Models\Race;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Livewire\Wireable;

final readonly class Character implements Wireable
{
    public int $age;
    /**
     * @param Collection<Implant> $implants
     */
    public function __construct(
        public string $name,
        public string $description,
        public string $gender,
        public Race $race,
        public Bloodline $bloodline,
        public CarbonInterface $birthday,
        public Portrait $portrait,
        public float $securityStatus,
        public Attributes $attributes,
        public Collection $implants,
    ) {
        $this->age = $this->birthday->diffInYears();
        $this->implants->sortBy('slot');
    }

    #[\Override]
    public function toLivewire(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'gender' => $this->gender,
            'race' => $this->race,
            'bloodline' => $this->bloodline,
            'birthday' => $this->birthday,
            'portrait' => $this->portrait,
            'securityStatus' => $this->securityStatus,
            'attributes' => $this->attributes,
            'implants' => $this->implants,
        ];
    }

    #[\Override]
    public static function fromLivewire($value): Character
    {
        return new Character(
            name: $value['name'],
            description: $value['description'],
            gender: $value['gender'],
            race: $value['race'],
            bloodline: $value['bloodline'],
            birthday: $value['birthday'],
            portrait: $value['portrait'],
            securityStatus: $value['securityStatus'],
            attributes: $value['attributes'],
            implants: $value['implants'],
        );
    }
}
