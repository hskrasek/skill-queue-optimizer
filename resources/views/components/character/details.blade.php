@props(['character'])
@php
    /** @var \App\ESI\Character $character */
@endphp
<dl class="grid grid-cols-1 sm:grid-cols-2">
    <div class="px-4 py-1 sm:col-span-1 sm:px-0">
        <dt class="text-sm font-medium leading-6 text-gray-900">Race</dt>
        <dd class="mt-1 text-sm leading-6 text-gray-700 sm:mt-2">{{ $character->race->raceName }}</dd>
    </div>
    <div class="px-4 py-1 sm:col-span-1 sm:px-0">
        <dt class="text-sm font-medium leading-6 text-gray-900">Bloodline</dt>
        <dd class="mt-1 text-sm leading-6 text-gray-700 sm:mt-2">{{ $character->bloodline->bloodlineName }}</dd>
    </div>
    <div class="px-4 py-1 sm:col-span-1 sm:px-0">
        <dt class="text-sm font-medium leading-6 text-gray-900">Security Status</dt>
        <dd class="mt-1 text-sm leading-6 text-gray-700 sm:mt-2">{{ round($character->securityStatus, 2) }}</dd>
    </div>
    <div class="px-4 py-1 sm:col-span-1 sm:px-0">
        <dt class="text-sm font-medium leading-6 text-gray-900">Birthdate</dt>
        <dd class="mt-1 text-sm leading-6 text-gray-700 sm:mt-2">{{ $character->birthday->toFormattedDayDateString() }}</dd>
    </div>
</dl>
