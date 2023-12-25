@props(['character'])
@php
    /** @var \App\ESI\Character $character */
@endphp
<div
    x-data="optimal"
    class="float-right ml-4 mb-2 mr-4 overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg text-neutral-800 border border-eve-dark border-solid rounded bg-clip-content bg-origin-border">
    <div class="flow-root">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Attribute
                            </th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Total</th>
                            <th scope="col" class="relative py-3.5 pl-3 pr-3 sm:pr-3">
                                <button @click="optimize"
                                        type="button"
                                        class="block rounded-md bg-eve-accent px-1.5 py-1 text-center text-sm font-semibold text-white shadow-sm hover:bg-eve-accent focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                    Optimize
                                </button>
                            </th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach($character->attributes->values() as $attribute => $value)
                            <tr>
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ ucfirst($attribute->value) }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $value }}</td>
                                <td x-text="{{ ucfirst($attribute->value) }}"
                                    class="whitespace-nowrap px-1 py-2 text-md text-gray-500 text-center">
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
