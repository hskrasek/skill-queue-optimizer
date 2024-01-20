@php
    /** @var \App\ESI\Character $character */
    /** @var \Illuminate\Support\Collection<int, \App\ESI\SkillQueueItem> $skillQueue */
@endphp
<div id="skillQueue" class="mt-4">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <p class="mt-2 text-base font-semibold leading-6 text-gray-900">
                Skill queue finishes in: <span class="text-sm font-normal">{{ $skillQueue->queueTime($character->attributes) }}</span>
            </p>
            @if($optimalAttributes)
                <p class="mt-2 text-base font-semibold leading-6 text-gray-900">
                    With optimal attributes: <span class="text-sm font-normal">{{ $skillQueue->queueTime($optimalAttributes) }}</span>
                    <br />
                    <span class="text-xs text-green-500">
                        You save {{ $skillQueue->queueTimeInterval($character->attributes)->subtract($skillQueue->queueTimeInterval($optimalAttributes))->forHumans(['short' => false, 'parts' => 3, 'join' => true, 'skip' => ['year', 'month', 'weeks', ]]) }}!
                    </span>
                </p>
            @endif
        </div>
        {{--            <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">--}}
        {{--                <button type="button" class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Add user</button>--}}
        {{--            </div>--}}
    </div>
    <div class="-mx-4 mt-6 sm:-mx-0">
        <table class="min-w-full divide-y divide-gray-300">
            <thead>
            <tr>
                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                    Skill
                </th>
                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Training Time</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
            @foreach($skillQueue as $skillQueueItem)
                <tr>
                    <td class="w-full max-w-0 py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:w-auto sm:max-w-none sm:pl-0">
                        {{ $skillQueueItem->skill->name }} {{ $skillQueueItem->queuedSkill->finishedLevel() }}
{{--                        <div>--}}
{{--                            {!! $skillQueueItem->queuedSkill->levelProgress()->toXMLString(false) !!}--}}
{{--                        </div>--}}
                        <div class="mt-1 truncate font-normal text-gray-500">{{ $skillQueueItem->skill->category }}</div>
                        <dl class="font-normal lg:hidden">
                            <dt class="sr-only sm:hidden">Training Time</dt>
                            <dd class="mt-1 truncate text-gray-500 sm:hidden">
                                {{ $skillQueueItem->trainingTime($character->attributes)->ceilHour()->forHumans(['short' => true, 'parts' => 3, 'join' => true, 'skip' => ['year', 'month', 'weeks', 'minutes']]) }}
                            </dd>
                        </dl>
                    </td>
                    <td class="px-3 py-4 text-sm text-gray-500">
                        {{ $skillQueueItem->trainingTime($character->attributes)->ceilHour()->forHumans(['short' => true, 'parts' => 3, 'join' => true, 'skip' => ['year', 'month', 'weeks', 'minutes']]) }}
                        @if($optimalAttributes)
                            <div class="mt-1 truncate font-normal text-emerald-500">
                                {{ $skillQueueItem->trainingTime($optimalAttributes)->ceilHour()->forHumans(['short' => true, 'parts' => 3, 'join' => true, 'skip' => ['year', 'month', 'weeks', 'minutes']]) }}
                            </div>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
