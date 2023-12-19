<?php

declare(strict_types=1);

namespace App\ESI;

use App\Models\Bloodline;
use App\Models\Race;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

final readonly class Character
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
}
