@props(['character'])
@php
    /** @var \App\ESI\Character $character */
@endphp
<div class="flex flex-wrap break-words px-2 py-4 leading-6 text-neutral-800">
    <div class="mt-0 w-full max-w-full flex-none break-words px-3 leading-6 text-neutral-800">
        <div class="float-left mr-4 text-neutral-800">
            <img src="{{ $character->portrait->x512 }}"
                 srcset="{{ $character->portrait->x64 }} 164w,
                         {{ $character->portrait->x128 }} 128w,
                         {{ $character->portrait->x256 }} 256w,
                         {{ $character->portrait->x512 }} 512w"
                 alt="Player portrait for the Eve Online character {{ $character->name }}"
                 width="150" height="150" class="rounded-full h-auto max-w-full align-middle">
        </div>
        <x-character.attributes :character="$character" />

        <x-character.details :character="$character" />

        <p class="my-4 text-neutral-800">
            @if(!empty($character->description))
                {{ $character->description }}
            @else
                {{ $character->bloodline->description }}
            @endif
        </p>
    </div>
</div>
