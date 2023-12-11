<?php

declare(strict_types=1);

namespace App\ESI;

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
        public CarbonInterface $birthday,
        public float $securityStatus,
        public Attributes $attributes,
        public Collection $implants,
    ) {
        $this->age = $this->birthday->diffInYears();
    }
}
