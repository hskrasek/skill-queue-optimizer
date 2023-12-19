@props(['character'])
@php
    /** @var \App\ESI\Character $character */
@endphp
<div class="flex flex-wrap break-words px-2 py-4 leading-6 text-neutral-800">
    <div class="mt-0 w-full max-w-full flex-none break-words px-3 leading-6 text-neutral-800 lg:w-7/12 lg:flex-none">
        <div class="float-left mb-2 mr-4 text-neutral-800">
            <img src="{{ $character->portrait->x512 }}"
                 alt="Player portrait for the Eve Online character {{ $character->name }}"
                 width="150" height="150" class="rounded-full h-auto max-w-full align-middle">
        </div>
        <div class="float-right mb-2 mr-4 text-neutral-800">
            <table class="divide-y divide-gray-300">
                <thead class="bg-gray-50">
                <tr>
                    <th scope="col"
                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                        Attribute
                    </th>
                    <th scope="col"
                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                        Value
                    </th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                @foreach($character->attributes->values() as $attribute => $value)
                    <tr>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ ucfirst($attribute->value) }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $value }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <h5 class="leading-2 mb-2 mt-0 font-sans text-xl font-semibold text-gray-600">
            {{ $character->name }}
        </h5>
        <h6 class="leading-2 mb-2 mt-0 font-sans text-base font-medium text-gray-600">
            <span
                class="font-semibold">Race: </span>{{ $character->race->raceName }}
        </h6>
        <h6 class="leading-2 mb-2 mt-0 font-sans text-base font-medium text-gray-600">
            <span
                class="font-semibold">Bloodline: </span>{{ $character->bloodline->bloodlineName }}
        </h6>
        <h6 class="leading-2 mb-2 mt-0 font-sans text-base font-medium text-gray-600">
            <span
                class="font-semibold">Security Status: </span>{{ round($character->securityStatus, 2) }}
        </h6>
        <h6 class="leading-2 mb-2 mt-0 font-sans text-base font-medium text-gray-600">
            <span
                class="font-semibold">Birthdate: </span>{{ $character->birthday->toFormattedDayDateString() }}
        </h6>

        <p class="mb-4 mt-0 text-neutral-800">
            @if(!empty($character->description))
                {{ $character->description }}
            @else
                {{ $character->bloodline->description }}
            @endif
        </p>
    </div>
</div>
