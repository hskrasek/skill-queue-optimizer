@php
    /** @var \Illuminate\Support\Collection<string, int> $attributes */
    /** @var \Illuminate\Support\Collection<int, \App\ESI\Skill> $skillQueue */
    /** @var array<string, int> $optimal */
@endphp
    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Eve Online Skill Queue Optimizer</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet"/>

    <!-- Styles -->
    @vite('resources/js/app.js')
</head>
<body class="antialiased">
<div
    class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white">
    <div class="bg-white px-6 py-32 lg:px-8">
        <div class="mx-auto max-w-3xl text-base leading-7 text-gray-700">
            <p class="text-base font-semibold leading-7 text-indigo-600">Eve Online</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Skill Queue Optimizer</h1>
{{--            <p class="mt-6 text-xl leading-8">Aliquet nec orci mattis amet quisque ullamcorper neque, nibh sem. At arcu,--}}
{{--                sit dui mi, nibh dui, diam eget aliquam. Quisque id at vitae feugiat egestas ac. Diam nulla orci at in--}}
{{--                viverra scelerisque eget. Eleifend egestas fringilla sapien.</p>--}}
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
            <div class="mt-16 max-w-2xl">
                <h2 class="text-2xl font-bold tracking-tight text-gray-900">Attributes</h2>
                <div class="flex flex-row justify-around gap-4">
                    <div class="basis-1/2">
                        <h3 class="text-xl font-bold tracking-tight text-gray-900">Current</h3>
                        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="inline-block min-w-full py-4 align-middle sm:px-6 lg:px-8">
                                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-300">
                                        <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Attribute</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Value</th>
                                        </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 bg-white">
                                        @foreach($attributes as $attribute => $value)
                                            <tr>
                                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ ucfirst($attribute) }}</td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $value }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
{{--                        <span class="text-base font-bold">Finishes In: </span><p class="text-base font-semibold leading-7 text-indigo-600">{{ $finishedTime }}</p>--}}
                    </div>
                    <div class="basis-1/2">
                        <h3 class="text-xl font-bold tracking-tight text-gray-900">Optimal</h3>
                        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="inline-block min-w-full py-4 align-middle sm:px-6 lg:px-8">
                                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-300">
                                        <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Attribute</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Value</th>
                                        </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 bg-white">
                                        @foreach($optimal as $attribute => $value)
                                            <tr>
                                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ ucfirst($attribute) }}</td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $value }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
{{--                        <span class="text-base font-bold">Finishes In: </span><p class="text-base font-semibold leading-7 text-indigo-600">{{ $optimalFinishTime }}</p>--}}
{{--                        <span class="text-base font-bold">Time Saved: </span><p class="text-base font-semibold leading-7 text-indigo-600">{{ $savedTime }}</p>--}}
                    </div>
                </div>
{{--                <div class="border-l-4 border-yellow-400 bg-yellow-50 p-4">--}}
{{--                    <div class="flex">--}}
{{--                        <div class="flex-shrink-0">--}}
{{--                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">--}}
{{--                                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />--}}
{{--                            </svg>--}}
{{--                        </div>--}}
{{--                        <div class="ml-3">--}}
{{--                            <p class="text-sm text-yellow-700">--}}
{{--                                Current training time calculations are not 100% accurate. Still working on converting the Swift code to PHP to get the maths right.--}}
{{--                            </p>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
            </div>
        </div>
    </div>
</div>
</body>
</html>
