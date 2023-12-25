@php
    /** @var \App\ESI\Character $character */
    /** @var \Illuminate\Support\Collection<int, \App\ESI\Skill> $skillQueue */
    /** @var array<string, int> $optimal */
@endphp
<x-layout>
    <div
        class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-eve-light dark:bg-dots-lighter dark:bg-eve-dark selection:bg-eve-accent selection:text-white">
        <div class="bg-white px-6 py-32 lg:px-8">
            <div class="mx-auto max-w-3xl text-base leading-7 text-gray-700">
                <p class="text-base font-semibold leading-7 text-eve-accent">Eve Online</p>
                <h1 class="mt-2 text-3xl font-bold tracking-tight text-eve-dark sm:text-4xl">{{ $character->name }}</h1>
                <div class="divide-y divide-gray-200">
                    <x-character :character="$character" />
                    <div class="mt-8 flow-root">
                        <div class="-mx-4 -my-2 sm:-mx-6 lg:-mx-8">
                            <div class="inline-block min-w-full py-2 align-middle">
                                <table class="min-w-full border-separate border-spacing-0">
                                    <thead>
                                    <tr>
                                        <th scope="col"
                                            class="sticky top-0 z-10 border-b border-gray-300 bg-white bg-opacity-75 py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8">
                                            Category
                                        </th>
                                        <th scope="col"
                                            class="sticky top-0 z-10 hidden border-b border-gray-300 bg-white bg-opacity-75 px-3 py-3.5 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter sm:table-cell">
                                            Skill
                                        </th>
                                        <th scope="col"
                                            class="sticky top-0 z-10 border-b border-gray-300 bg-white bg-opacity-75 px-3 py-3.5 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter">
                                            Level
                                        </th>
                                        <th scope="col"
                                            class="sticky top-0 z-10 hidden border-b border-gray-300 bg-white bg-opacity-75 px-3 py-3.5 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter lg:table-cell">
                                            Primary
                                        </th>
                                        <th scope="col"
                                            class="sticky top-0 z-10 border-b border-gray-300 bg-white bg-opacity-75 px-3 py-3.5 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter">
                                            Secondary
                                        </th>
                                        <th scope="col"
                                            class="sticky top-0 z-10 border-b border-gray-300 bg-white bg-opacity-75 px-2.5 py-3.5 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter">
                                            Finishes In
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($skillQueue as $skill)
                                        <tr>
                                            <td class="whitespace-nowrap border-b border-gray-200 py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6 lg:pl-8">
                                                {{ $skill->category }}
                                            </td>
                                            <td class="whitespace-nowrap border-b border-gray-200 hidden px-3 py-4 text-sm text-gray-500 sm:table-cell">
                                                {{ $skill->name }}
                                            </td>
                                            <td class="whitespace-nowrap border-b border-gray-200 hidden px-3 py-4 text-sm text-gray-500 lg:table-cell">
                                                {{ $skill->level }}
                                            </td>
                                            <td class="whitespace-nowrap border-b border-gray-200 px-3 py-4 text-sm text-gray-500">
                                                {{ $skill->primaryAttribute }}
                                            </td>
                                            <td class="whitespace-nowrap border-b border-gray-200 px-3 py-4 text-sm text-gray-500">
                                                {{ $skill->secondaryAttribute }}
                                            </td>
                                            <td class="whitespace-nowrap border-b border-gray-200 px-3 py-4 text-sm text-gray-500">
                                                {{ $skill->finishesIn() }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
