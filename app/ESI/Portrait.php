<?php

declare(strict_types=1);

namespace App\ESI;

final readonly class Portrait
{
    public function __construct(
        public string $x64,
        public string $x128,
        public string $x256,
        public string $x512,
    ) {}
}
