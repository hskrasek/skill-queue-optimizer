<?php

declare(strict_types=1);

namespace App\ESI;

use Livewire\Wireable;

final readonly class Portrait implements Wireable
{
    public function __construct(
        public string $x64,
        public string $x128,
        public string $x256,
        public string $x512,
    ) {}

    #[\Override]
    public function toLivewire(): array
    {
        return [
            'x64' => $this->x64,
            'x128' => $this->x128,
            'x256' => $this->x256,
            'x512' => $this->x512,
        ];
    }

    #[\Override]
    public static function fromLivewire($value): Portrait
    {
        return new Portrait(
            $value['x64'],
            $value['x128'],
            $value['x256'],
            $value['x512'],
        );
    }
}
