@php
    /** @var \App\ESI\Character $character */
    /** @var \Illuminate\Support\Collection<int, \App\ESI\Skill> $skillQueue */
@endphp
<x-layout>
    <div
        class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-eve-light dark:bg-dots-lighter dark:bg-eve-dark selection:bg-eve-accent selection:text-white">
        <div class="bg-white px-6 py-32 lg:px-8">
            <div class="mx-auto max-w-3xl text-base leading-7 text-gray-700">
                <p class="text-base font-semibold leading-7 text-eve-accent">Eve Online</p>
                <h1 class="mt-2 text-3xl font-bold tracking-tight text-eve-dark sm:text-4xl">{{ $character->name }}</h1>

                <x-character :character="$character" :skill-queue="$skillQueue"/>
                <div class="border-b border-gray-200 pb-5 sm:pb-0">
                    <div class="mt-3 sm:mt-4">
                        <!-- Dropdown menu on small screens -->
                        <div class="sm:hidden">
                            <label for="current-tab" class="sr-only">Select a tab</label>
                            <select id="current-tab" name="current-tab"
                                    class="block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                                <option selected>Skill Queue</option>
                                <option>Coming</option>
                                <option>Soon...</option>
                            </select>
                        </div>
                        <!-- Tabs at small breakpoint and up -->
                        <div class="hidden sm:block">
                            <nav class="-mb-px flex space-x-8">
                                <!-- Current: "border-indigo-500 text-indigo-600", Default: "border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700" -->
                                <a href="#skillQueue"
                                   class="border-indigo-500 text-indigo-600 whitespace-nowrap border-b-2 px-1 pb-4 text-sm font-medium"
                                   aria-current="page">Skill Queue</a>
                                <a href="#"
                                   class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 whitespace-nowrap border-b-2 px-1 pb-4 text-sm font-medium">Coming</a>
                                <a href="#"
                                   class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 whitespace-nowrap border-b-2 px-1 pb-4 text-sm font-medium">Soon...</a>
                            </nav>
                        </div>
                    </div>
                </div>
                <livewire:character.skill-queue :character="$character" :skill-queue="$skillQueue"/>
            </div>
        </div>
    </div>
</x-layout>
