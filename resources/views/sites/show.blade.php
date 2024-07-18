<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ env('APP_NAME') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="w-full relative sm:w-[200px]">
                <div x-data="{ open: false }" class="overflow-hidden truncate w-full sm:w-[200px]" data-has-alpine-state="true">
                    <button data-tooltip="Switch to another site" @click="open = !open" class="rounded-md bg-white px-3 h-10 text-sm duration-200 ease-in-out text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 hover:bg-purple-50 hover:border-purple-300 hover:text-purple-600 hover:ring-purple-700/10 font-medium transition-all inline-flex items-center justify-between mb-1 w-full sm:w-[200px]">
                        @php
                        $cleanedUrl = \Illuminate\Support\Str::replaceFirst('http://', '', $site->site_url);
                        $cleanedUrl = \Illuminate\Support\Str::replaceFirst('https://', '', $cleanedUrl);
                        $cleanedUrl = rtrim($cleanedUrl, '/');
                        @endphp
                        <span>{{ $cleanedUrl }}</span>
                        <svg class="ml-1 w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                            <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"></path>
                        </svg> </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1" x-transform:origin="top right" class="absolute right-0 z-10 mt-2 w-full" @click.away="open = false" style="display: none;">
                        <div class="w-full rounded-lg bg-white text-sm leading-6 shadow-lg ring-1 ring-gray-900/5">
                            <div class="border-b border-gray-100">
                                <a href="/dashboard/sites" class="block w-full text-left px-4 py-3 text-sm font-medium text-gray-700 hover:bg-purple-50 hover:text-purple-600 focus:outline-none">
                                    <span>← Back to all sites</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="overflow-x-auto">
                        <dl class="mx-auto grid grid-cols-1 gap-px bg-gray-900/5 border-gray-100 border border-b-0 sm:grid-cols-2 lg:grid-cols-4">
                            <div class="relative flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2 bg-white px-4 py-6 sm:px-6 lg:px-8">
                                <dt class="text-sm font-medium leading-6 text-gray-500">Indexed pages</dt>
                                <dd class="text-xs font-medium text-green-700">
                                    @php
                                    $total = $totalIndexedPages + $inProgress + $notIndexed + $notChecked;
                                    $percentage = $total > 0 ? round(($totalIndexedPages / $total) * 100, 2) : 0;
                                    @endphp
                                    {{ $percentage }}%
                                </dd>

                                <dd class="cursor-pointer w-full flex-none text-4xl p-3 font-medium leading-10 tracking-tight text-gray-900" wire:click="$set('filters.status', ['indexed', 'likely_indexed'])" data-tooltip="This is the number of pages that have been indexed by Google on {{ $site->site_url }}">
                                    {{ $totalIndexedPages }}
                                </dd>

                                <dd class="relative -ml-4 leading-tight xl:absolute bottom-0 w-full self-end text-sm font-medium tracking-tight text-gray-500">
                                    <div class="flex items-center pb-3">
                                        <svg class="w-5 h-5 mr-1 cursor-pointer" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z"></path>
                                        </svg> <span wire:click="$set('filters.status', ['indexed_by_um'])" class="cursor-pointer">{{ $totalIndexedPages }} pages indexed as of {{ \Carbon\Carbon::now()->format('M d, Y') }}</span>
                                    </div>
                                </dd>
                            </div>
                            <div class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2 bg-white px-4 py-6 sm:px-6 lg:px-8">
                                <dt class="text-sm font-medium leading-6 text-gray-500">In progress</dt>
                                <dd class="text-xs font-medium text-amber-600">
                                    @php
                                    $percentageInProgress = $total > 0 ? round(($inProgress / $total) * 100, 2) : 0;
                                    @endphp
                                    {{ $percentageInProgress }}%
                                </dd>
                                <dd class="cursor-pointer w-full flex-none text-4xl p-3 font-medium leading-10 tracking-tight text-gray-900" wire:click="$set('filters.status', ['submitted'])" data-tooltip="This is the number of pages that are currently being submitted to Google for indexing for {{ $site->site_url }}">
                                    {{ $inProgress }}
                                </dd>
                            </div>
                            <div class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2 bg-white px-4 py-6 sm:px-6 lg:px-8">
                                <dt class="text-sm font-medium leading-6 text-gray-500">Not indexed</dt>
                                <dd class="text-xs font-medium text-red-600">
                                    @php
                                    $percentageNotIndexed = $total > 0 ? round(($notIndexed / $total) * 100, 2) : 0;
                                    @endphp
                                    {{ $percentageNotIndexed }}%
                                </dd>
                                <dd class="cursor-pointer w-full flex-none text-4xl p-3 font-medium leading-10 tracking-tight text-gray-900" wire:click="$set('filters.status', ['not_indexed'])" data-tooltip="This is the number of pages that have not been indexed in Google for {{ $site->site_url }}">
                                    {{ $notIndexed }}
                                </dd>
                            </div>
                            <div class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2 bg-white px-4 py-6 sm:px-6 lg:px-8">
                                <dt class="text-sm font-medium leading-6 text-gray-500">Not checked</dt>
                                <dd class="text-xs font-medium text-blue-600">
                                    @php
                                    $percentageNotChecked = $total > 0 ? round(($notChecked / $total) * 100, 2) : 0;
                                    @endphp
                                    {{ $percentageNotChecked }}%
                                </dd>
                                <dd class="cursor-pointer w-full flex-none text-4xl p-3 font-medium leading-10 tracking-tight text-gray-900" wire:click="$set('filters.status', ['not_checked'])" data-tooltip="This is the number of pages that we have not checked the index status of yet.">
                                    {{ $notChecked }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="overflow-auto lg:overflow-hidden ring-1 ring-black ring-opacity-5 sm:rounded-lg mt-5">
                <table class="table-fixed min-w-full divide-y divide-gray-100">
                    <thead class="bg-black">
                        <tr>
                            <th scope="col" class="px-6 py-4 pr-0 text-left text-sm font-medium text-gray-50 tracking-wider w-3">
                                <div class="flex h-6 items-center">
                                    <input wire:model.live="selectPage" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-purple-600 focus:ring-purple-600 cursor-pointer">
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-sm text-gray-50 tracking-wider w-96 font-medium" wire:click="sortBy('url')">
                                <span class="flex items-center cursor-pointer">
                                    Page
                                    <svg class="w-4 h-4 ml-1 text-gray-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                        <path fill-rule="evenodd" d="M10.53 3.47a.75.75 0 0 0-1.06 0L6.22 6.72a.75.75 0 0 0 1.06 1.06L10 5.06l2.72 2.72a.75.75 0 1 0 1.06-1.06l-3.25-3.25Zm-4.31 9.81 3.25 3.25a.75.75 0 0 0 1.06 0l3.25-3.25a.75.75 0 1 0-1.06-1.06L10 14.94l-2.72-2.72a.75.75 0 0 0-1.06 1.06Z" clip-rule="evenodd"></path>
                                    </svg>
                                </span>
                            </th>
                            <th scope="col" class="px-6 py-4 text-center text-sm text-gray-50 tracking-wider font-medium" wire:click="sortBy('status')">
                                <span class="flex items-center justify-center cursor-pointer">
                                    Index Status
                                    <svg class="w-4 h-4 ml-1 text-gray-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                        <path fill-rule="evenodd" d="M10.53 3.47a.75.75 0 0 0-1.06 0L6.22 6.72a.75.75 0 0 0 1.06 1.06L10 5.06l2.72 2.72a.75.75 0 1 0 1.06-1.06l-3.25-3.25Zm-4.31 9.81 3.25 3.25a.75.75 0 0 0 1.06 0l3.25-3.25a.75.75 0 1 0-1.06-1.06L10 14.94l-2.72-2.72a.75.75 0 0 0-1.06 1.06Z" clip-rule="evenodd"></path>
                                    </svg>
                                </span>
                            </th>
                            <th scope="col" class="px-6 py-4 text-center text-sm text-gray-50 tracking-wider font-medium" wire:click="sortBy('submitted_at')">
                                <span class="flex items-center justify-center cursor-pointer">
                                    Last Submitted
                                    <svg class="w-4 h-4 ml-1 text-gray-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                        <path fill-rule="evenodd" d="M10.53 3.47a.75.75 0 0 0-1.06 0L6.22 6.72a.75.75 0 0 0 1.06 1.06L10 5.06l2.72 2.72a.75.75 0 1 0 1.06-1.06l-3.25-3.25Zm-4.31 9.81 3.25 3.25a.75.75 0 0 0 1.06 0l3.25-3.25a.75.75 0 1 0-1.06-1.06L10 14.94l-2.72-2.72a.75.75 0 0 0-1.06 1.06Z" clip-rule="evenodd"></path>
                                    </svg>
                                </span>
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-sm font-medium text-gray-50 tracking-wider hidden md:table-cell w-80">
                                Page Health
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100" wire:loading.class="opacity-30" wire:target="toggleSelection">
                        <tr wire:key="page-53303336">
                            <td class="w-3 px-6 py-4 pr-0 whitespace-nowrap text-center text-md text-gray-500">
                                <input type="checkbox" id="checkbox-53303336" class="h-4 w-4 rounded border-gray-300 text-purple-600 focus:ring-purple-600 cursor-pointer" value="53303336" wire:model="selected.53303336.checked" wire:click="toggleSelection(53303336)">
                            </td>
                            <td class="px-6 py-4 text-md whitespace-nowrap font-medium text-gray-900 text-ellipsis overflow-hidden max-w-xs">
                                <a class="hover:text-purple-600 hover:underline" href="https://hrefcreative.com/" target="_blank" rel="noopener noreferrer">
                                    /
                                </a>
                                <br>
                                <button type="button" class="inline-flex items-center gap-x-1.5 text-xs text-purple-600" x-tooltip.raw="This page has had 0 impressions in Google search in the last 30 days.">
                                    <svg class="w-3 h-3 cursor-pointer" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                        <path d="M10 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"></path>
                                        <path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 0 1 0-1.186A10.004 10.004 0 0 1 10 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0 1 10 17c-4.257 0-7.893-2.66-9.336-6.41ZM14 10a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" clip-rule="evenodd"></path>
                                    </svg>
                                    0
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                <div class="flex items-center justify-center">
                                    <svg x-tooltip.raw="This page was submitted on Jul 15, 2024 and indexed on Jul 15, 2024" class="w-6 h-6 mr-2 cursor-pointer text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                        <path fill-rule="evenodd" d="M16.403 12.652a3 3 0 0 0 0-5.304 3 3 0 0 0-3.75-3.751 3 3 0 0 0-5.305 0 3 3 0 0 0-3.751 3.75 3 3 0 0 0 0 5.305 3 3 0 0 0 3.75 3.751 3 3 0 0 0 5.305 0 3 3 0 0 0 3.751-3.75Zm-2.546-4.46a.75.75 0 0 0-1.214-.883l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                <span class="text-gray-600">
                                    Jul 15, 2024
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-md text-gray-500 hidden md:table-cell">
                                <div class="flex justify-left">
                                    <a href="https://urlmonitor.com/sites/31812/sitemaps">
                                        <svg class="w-6 h-6 mr-2 cursor-pointer text-green-500" x-tooltip.raw="Included in the following sitemap: https://hrefcreative.com/page-sitemap.xml" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                            <path fill="currentColor" d="M472,328H448V264a24.027,24.027,0,0,0-24-24H272V176h32a24.028,24.028,0,0,0,24-24V80a24.028,24.028,0,0,0-24-24H208a24.028,24.028,0,0,0-24,24v72a24.028,24.028,0,0,0,24,24h32v64H88a24.027,24.027,0,0,0-24,24v64H40a24.028,24.028,0,0,0-24,24v72a24.028,24.028,0,0,0,24,24h80a24.028,24.028,0,0,0,24-24V352a24.028,24.028,0,0,0-24-24H96V272H240v56H216a24.028,24.028,0,0,0-24,24v72a24.028,24.028,0,0,0,24,24h80a24.028,24.028,0,0,0,24-24V352a24.028,24.028,0,0,0-24-24H272V272H416v56H392a24.028,24.028,0,0,0-24,24v72a24.028,24.028,0,0,0,24,24h80a24.028,24.028,0,0,0,24-24V352A24.028,24.028,0,0,0,472,328ZM216,88h80v56H216ZM112,360v56H48V360Zm176,0v56H224V360Zm176,56H400V360h64Z"></path>
                                        </svg>
                                    </a>
                                    <svg x-tooltip.raw="This page was last checked on Jul 15, 2024" class="w-6 h-6 mr-2 cursor-pointer text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                        <path d="M8 10a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0Z"></path>
                                        <path fill-rule="evenodd" d="M4.5 2A1.5 1.5 0 0 0 3 3.5v13A1.5 1.5 0 0 0 4.5 18h11a1.5 1.5 0 0 0 1.5-1.5V7.621a1.5 1.5 0 0 0-.44-1.06l-4.12-4.122A1.5 1.5 0 0 0 11.378 2H4.5Zm5 5a3 3 0 1 0 1.524 5.585l1.196 1.195a.75.75 0 1 0 1.06-1.06l-1.195-1.196A3 3 0 0 0 9.5 7Z" clip-rule="evenodd"></path>
                                    </svg>
                                    <svg x-tooltip.raw="This page was last crawled on Jul 12, 2024" class="w-6 h-6 mr-2 cursor-pointer text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                        <path fill-rule="evenodd" d="M6.56 1.14a.75.75 0 0 1 .177 1.045 3.989 3.989 0 0 0-.464.86c.185.17.382.329.59.473A3.993 3.993 0 0 1 10 2c1.272 0 2.405.594 3.137 1.518.208-.144.405-.302.59-.473a3.989 3.989 0 0 0-.464-.86.75.75 0 0 1 1.222-.869c.369.519.65 1.105.822 1.736a.75.75 0 0 1-.174.707 7.03 7.03 0 0 1-1.299 1.098A4 4 0 0 1 14 6c0 .52-.301.963-.723 1.187a6.961 6.961 0 0 1-1.158.486c.13.208.231.436.296.679 1.413-.174 2.779-.5 4.081-.96a19.655 19.655 0 0 0-.09-2.319.75.75 0 1 1 1.493-.146 21.239 21.239 0 0 1 .08 3.028.75.75 0 0 1-.482.667 20.873 20.873 0 0 1-5.153 1.249 2.521 2.521 0 0 1-.107.247 20.945 20.945 0 0 1 5.252 1.257.75.75 0 0 1 .482.74 20.945 20.945 0 0 1-.908 5.107.75.75 0 0 1-1.433-.444c.415-1.34.69-2.743.806-4.191-.495-.173-1-.327-1.512-.46.05.284.076.575.076.873 0 1.814-.517 3.312-1.426 4.37A4.639 4.639 0 0 1 10 19a4.639 4.639 0 0 1-3.574-1.63C5.516 16.311 5 14.813 5 13c0-.298.026-.59.076-.873-.513.133-1.017.287-1.512.46.116 1.448.39 2.85.806 4.191a.75.75 0 1 1-1.433.444 20.94 20.94 0 0 1-.908-5.107.75.75 0 0 1 .482-.74 20.838 20.838 0 0 1 5.252-1.257 2.493 2.493 0 0 1-.107-.247 20.874 20.874 0 0 1-5.153-1.249.75.75 0 0 1-.482-.667 21.342 21.342 0 0 1 .08-3.028.75.75 0 1 1 1.493.146 19.745 19.745 0 0 0-.09 2.319c1.302.46 2.668.786 4.08.96.066-.243.166-.471.297-.679a6.962 6.962 0 0 1-1.158-.486A1.348 1.348 0 0 1 6 6a4 4 0 0 1 .166-1.143 7.032 7.032 0 0 1-1.3-1.098.75.75 0 0 1-.173-.707 5.48 5.48 0 0 1 .822-1.736.75.75 0 0 1 1.046-.177Z" clip-rule="evenodd"></path>
                                    </svg>
                                    <svg x-tooltip.raw="The user and Google selected canonical URLs are both https://hrefcreative.com/." class="w-6 h-6 mr-2 cursor-pointer text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                        <path d="M12.232 4.232a2.5 2.5 0 0 1 3.536 3.536l-1.225 1.224a.75.75 0 0 0 1.061 1.06l1.224-1.224a4 4 0 0 0-5.656-5.656l-3 3a4 4 0 0 0 .225 5.865.75.75 0 0 0 .977-1.138 2.5 2.5 0 0 1-.142-3.667l3-3Z"></path>
                                        <path d="M11.603 7.963a.75.75 0 0 0-.977 1.138 2.5 2.5 0 0 1 .142 3.667l-3 3a2.5 2.5 0 0 1-3.536-3.536l1.225-1.224a.75.75 0 0 0-1.061-1.06l-1.224 1.224a4 4 0 1 0 5.656 5.656l3-3a4 4 0 0 0-.225-5.865Z"></path>
                                    </svg>
                                    <svg x-tooltip.raw="This page is allowed to be indexed in Google." class="w-6 h-6 mr-2 cursor-pointer text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                        <path fill-rule="evenodd" d="M10 1c3.866 0 7 1.79 7 4s-3.134 4-7 4-7-1.79-7-4 3.134-4 7-4Zm5.694 8.13c.464-.264.91-.583 1.306-.952V10c0 2.21-3.134 4-7 4s-7-1.79-7-4V8.178c.396.37.842.688 1.306.953C5.838 10.006 7.854 10.5 10 10.5s4.162-.494 5.694-1.37ZM3 13.179V15c0 2.21 3.134 4 7 4s7-1.79 7-4v-1.822c-.396.37-.842.688-1.306.953-1.532.875-3.548 1.369-5.694 1.369s-4.162-.494-5.694-1.37A7.009 7.009 0 0 1 3 13.179Z" clip-rule="evenodd"></path>
                                    </svg>
                                    <svg x-tooltip.raw="This page has been successfully fetched by Google." class="w-6 h-6 mr-2 cursor-pointer text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                        <path fill-rule="evenodd" d="M8 1a.75.75 0 0 1 .75.75V6h-1.5V1.75A.75.75 0 0 1 8 1Zm-.75 5v3.296l-.943-1.048a.75.75 0 1 0-1.114 1.004l2.25 2.5a.75.75 0 0 0 1.114 0l2.25-2.5a.75.75 0 0 0-1.114-1.004L8.75 9.296V6h2A2.25 2.25 0 0 1 13 8.25v4.5A2.25 2.25 0 0 1 10.75 15h-5.5A2.25 2.25 0 0 1 3 12.75v-4.5A2.25 2.25 0 0 1 5.25 6h2ZM7 16.75v-.25h3.75a3.75 3.75 0 0 0 3.75-3.75V10h.25A2.25 2.25 0 0 1 17 12.25v4.5A2.25 2.25 0 0 1 14.75 19h-5.5A2.25 2.25 0 0 1 7 16.75Z" clip-rule="evenodd"></path>
                                    </svg>
                                    <a href="https://search.google.com/search-console/inspect?resource_id=https://hrefcreative.com/&id=fMB1sKp3bfAm342g3UD_Ig&utm_medium=link&utm_source=api" target="_blank" class="cursor-pointer self-end">
                                        <svg class="w-6 h-6 cursor-pointer text-gray-500 hover:text-gray-700" x-tooltip.raw="Inspect URL in Google Search Console" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">
                                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"></path>
                                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"></path>
                                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"></path>
                                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"></path>
                                            <path d="M1 1h22v22H1z" fill="none"></path>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <tr wire:key="page-53303337">
                            <td class="w-3 px-6 py-4 pr-0 whitespace-nowrap text-center text-md text-gray-500">
                                <input type="checkbox" id="checkbox-53303337" class="h-4 w-4 rounded border-gray-300 text-purple-600 focus:ring-purple-600 cursor-pointer" value="53303337" wire:model="selected.53303337.checked" wire:click="toggleSelection(53303337)">
                            </td>
                            <td class="px-6 py-4 text-md whitespace-nowrap font-medium text-gray-900 text-ellipsis overflow-hidden max-w-xs">
                                <a class="hover:text-purple-600 hover:underline" href="https://hrefcreative.com/social-media/" target="_blank" rel="noopener noreferrer">
                                    /social-media/
                                </a>
                                <br>
                                <button type="button" class="inline-flex items-center gap-x-1.5 text-xs text-purple-600" x-tooltip.raw="This page has had 0 impressions in Google search in the last 30 days.">
                                    <svg class="w-3 h-3 cursor-pointer" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                        <path d="M10 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"></path>
                                        <path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 0 1 0-1.186A10.004 10.004 0 0 1 10 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0 1 10 17c-4.257 0-7.893-2.66-9.336-6.41ZM14 10a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" clip-rule="evenodd"></path>
                                    </svg>
                                    0
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                <div class="flex items-center justify-center">
                                    <svg x-tooltip.raw="This page was submitted on Jul 15, 2024 and indexed on Jul 15, 2024" class="w-6 h-6 mr-2 cursor-pointer text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                        <path fill-rule="evenodd" d="M16.403 12.652a3 3 0 0 0 0-5.304 3 3 0 0 0-3.75-3.751 3 3 0 0 0-5.305 0 3 3 0 0 0-3.751 3.75 3 3 0 0 0 0 5.305 3 3 0 0 0 3.75 3.751 3 3 0 0 0 5.305 0 3 3 0 0 0 3.751-3.75Zm-2.546-4.46a.75.75 0 0 0-1.214-.883l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                <span class="text-gray-600">
                                    Jul 15, 2024
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-md text-gray-500 hidden md:table-cell">
                                <div class="flex justify-left">
                                    <a href="https://urlmonitor.com/sites/31812/sitemaps">
                                        <svg class="w-6 h-6 mr-2 cursor-pointer text-green-500" x-tooltip.raw="Included in the following sitemap: https://hrefcreative.com/page-sitemap.xml" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                            <path fill="currentColor" d="M472,328H448V264a24.027,24.027,0,0,0-24-24H272V176h32a24.028,24.028,0,0,0,24-24V80a24.028,24.028,0,0,0-24-24H208a24.028,24.028,0,0,0-24,24v72a24.028,24.028,0,0,0,24,24h32v64H88a24.027,24.027,0,0,0-24,24v64H40a24.028,24.028,0,0,0-24,24v72a24.028,24.028,0,0,0,24,24h80a24.028,24.028,0,0,0,24-24V352a24.028,24.028,0,0,0-24-24H96V272H240v56H216a24.028,24.028,0,0,0-24,24v72a24.028,24.028,0,0,0,24,24h80a24.028,24.028,0,0,0,24-24V352a24.028,24.028,0,0,0-24-24H272V272H416v56H392a24.028,24.028,0,0,0-24,24v72a24.028,24.028,0,0,0,24,24h80a24.028,24.028,0,0,0,24-24V352A24.028,24.028,0,0,0,472,328ZM216,88h80v56H216ZM112,360v56H48V360Zm176,0v56H224V360Zm176,56H400V360h64Z"></path>
                                        </svg>
                                    </a>
                                    <svg x-tooltip.raw="This page was last checked on Jul 15, 2024" class="w-6 h-6 mr-2 cursor-pointer text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                        <path d="M8 10a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0Z"></path>
                                        <path fill-rule="evenodd" d="M4.5 2A1.5 1.5 0 0 0 3 3.5v13A1.5 1.5 0 0 0 4.5 18h11a1.5 1.5 0 0 0 1.5-1.5V7.621a1.5 1.5 0 0 0-.44-1.06l-4.12-4.122A1.5 1.5 0 0 0 11.378 2H4.5Zm5 5a3 3 0 1 0 1.524 5.585l1.196 1.195a.75.75 0 1 0 1.06-1.06l-1.195-1.196A3 3 0 0 0 9.5 7Z" clip-rule="evenodd"></path>
                                    </svg>
                                    <svg x-tooltip.raw="This page was last crawled on Jun 16, 2024" class="w-6 h-6 mr-2 cursor-pointer text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                        <path fill-rule="evenodd" d="M6.56 1.14a.75.75 0 0 1 .177 1.045 3.989 3.989 0 0 0-.464.86c.185.17.382.329.59.473A3.993 3.993 0 0 1 10 2c1.272 0 2.405.594 3.137 1.518.208-.144.405-.302.59-.473a3.989 3.989 0 0 0-.464-.86.75.75 0 0 1 1.222-.869c.369.519.65 1.105.822 1.736a.75.75 0 0 1-.174.707 7.03 7.03 0 0 1-1.299 1.098A4 4 0 0 1 14 6c0 .52-.301.963-.723 1.187a6.961 6.961 0 0 1-1.158.486c.13.208.231.436.296.679 1.413-.174 2.779-.5 4.081-.96a19.655 19.655 0 0 0-.09-2.319.75.75 0 1 1 1.493-.146 21.239 21.239 0 0 1 .08 3.028.75.75 0 0 1-.482.667 20.873 20.873 0 0 1-5.153 1.249 2.521 2.521 0 0 1-.107.247 20.945 20.945 0 0 1 5.252 1.257.75.75 0 0 1 .482.74 20.945 20.945 0 0 1-.908 5.107.75.75 0 0 1-1.433-.444c.415-1.34.69-2.743.806-4.191-.495-.173-1-.327-1.512-.46.05.284.076.575.076.873 0 1.814-.517 3.312-1.426 4.37A4.639 4.639 0 0 1 10 19a4.639 4.639 0 0 1-3.574-1.63C5.516 16.311 5 14.813 5 13c0-.298.026-.59.076-.873-.513.133-1.017.287-1.512.46.116 1.448.39 2.85.806 4.191a.75.75 0 1 1-1.433.444 20.94 20.94 0 0 1-.908-5.107.75.75 0 0 1 .482-.74 20.838 20.838 0 0 1 5.252-1.257 2.493 2.493 0 0 1-.107-.247 20.874 20.874 0 0 1-5.153-1.249.75.75 0 0 1-.482-.667 21.342 21.342 0 0 1 .08-3.028.75.75 0 1 1 1.493.146 19.745 19.745 0 0 0-.09 2.319c1.302.46 2.668.786 4.08.96.066-.243.166-.471.297-.679a6.962 6.962 0 0 1-1.158-.486A1.348 1.348 0 0 1 6 6a4 4 0 0 1 .166-1.143 7.032 7.032 0 0 1-1.3-1.098.75.75 0 0 1-.173-.707 5.48 5.48 0 0 1 .822-1.736.75.75 0 0 1 1.046-.177Z" clip-rule="evenodd"></path>
                                    </svg>
                                    <svg x-tooltip.raw="The user and Google selected canonical URLs are both https://hrefcreative.com/social-media/." class="w-6 h-6 mr-2 cursor-pointer text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                        <path d="M12.232 4.232a2.5 2.5 0 0 1 3.536 3.536l-1.225 1.224a.75.75 0 0 0 1.061 1.06l1.224-1.224a4 4 0 0 0-5.656-5.656l-3 3a4 4 0 0 0 .225 5.865.75.75 0 0 0 .977-1.138 2.5 2.5 0 0 1-.142-3.667l3-3Z"></path>
                                        <path d="M11.603 7.963a.75.75 0 0 0-.977 1.138 2.5 2.5 0 0 1 .142 3.667l-3 3a2.5 2.5 0 0 1-3.536-3.536l1.225-1.224a.75.75 0 0 0-1.061-1.06l-1.224 1.224a4 4 0 1 0 5.656 5.656l3-3a4 4 0 0 0-.225-5.865Z"></path>
                                    </svg>
                                    <svg x-tooltip.raw="This page is allowed to be indexed in Google." class="w-6 h-6 mr-2 cursor-pointer text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                        <path fill-rule="evenodd" d="M10 1c3.866 0 7 1.79 7 4s-3.134 4-7 4-7-1.79-7-4 3.134-4 7-4Zm5.694 8.13c.464-.264.91-.583 1.306-.952V10c0 2.21-3.134 4-7 4s-7-1.79-7-4V8.178c.396.37.842.688 1.306.953C5.838 10.006 7.854 10.5 10 10.5s4.162-.494 5.694-1.37ZM3 13.179V15c0 2.21 3.134 4 7 4s7-1.79 7-4v-1.822c-.396.37-.842.688-1.306.953-1.532.875-3.548 1.369-5.694 1.369s-4.162-.494-5.694-1.37A7.009 7.009 0 0 1 3 13.179Z" clip-rule="evenodd"></path>
                                    </svg>
                                    <svg x-tooltip.raw="This page has been successfully fetched by Google." class="w-6 h-6 mr-2 cursor-pointer text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                        <path fill-rule="evenodd" d="M8 1a.75.75 0 0 1 .75.75V6h-1.5V1.75A.75.75 0 0 1 8 1Zm-.75 5v3.296l-.943-1.048a.75.75 0 1 0-1.114 1.004l2.25 2.5a.75.75 0 0 0 1.114 0l2.25-2.5a.75.75 0 0 0-1.114-1.004L8.75 9.296V6h2A2.25 2.25 0 0 1 13 8.25v4.5A2.25 2.25 0 0 1 10.75 15h-5.5A2.25 2.25 0 0 1 3 12.75v-4.5A2.25 2.25 0 0 1 5.25 6h2ZM7 16.75v-.25h3.75a3.75 3.75 0 0 0 3.75-3.75V10h.25A2.25 2.25 0 0 1 17 12.25v4.5A2.25 2.25 0 0 1 14.75 19h-5.5A2.25 2.25 0 0 1 7 16.75Z" clip-rule="evenodd"></path>
                                    </svg>
                                    <a href="https://search.google.com/search-console/inspect?resource_id=https://hrefcreative.com/&id=958obamvij8TK8V3v1qXFg&utm_medium=link&utm_source=api" target="_blank" class="cursor-pointer self-end">
                                        <svg class="w-6 h-6 cursor-pointer text-gray-500 hover:text-gray-700" x-tooltip.raw="Inspect URL in Google Search Console" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">
                                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"></path>
                                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"></path>
                                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"></path>
                                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"></path>
                                            <path d="M1 1h22v22H1z" fill="none"></path>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <tr wire:key="page-53303344">
                            <td class="w-3 px-6 py-4 pr-0 whitespace-nowrap text-center text-md text-gray-500">
                                <input type="checkbox" id="checkbox-53303344" class="h-4 w-4 rounded border-gray-300 text-purple-600 focus:ring-purple-600 cursor-pointer" value="53303344" wire:model="selected.53303344.checked" wire:click="toggleSelection(53303344)">
                            </td>
                            <td class="px-6 py-4 text-md whitespace-nowrap font-medium text-gray-900 text-ellipsis overflow-hidden max-w-xs">
                                <a class="hover:text-purple-600 hover:underline" href="https://hrefcreative.com/personal-branding-real-estate-agents-tips-best-practices/" target="_blank" rel="noopener noreferrer">
                                    /personal-branding-real-estate-agents-tips-best-practices/
                                </a>
                                <br>
                                <button type="button" class="inline-flex items-center gap-x-1.5 text-xs text-purple-600" x-tooltip.raw="This page has had 0 impressions in Google search in the last 30 days.">
                                    <svg class="w-3 h-3 cursor-pointer" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                        <path d="M10 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"></path>
                                        <path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 0 1 0-1.186A10.004 10.004 0 0 1 10 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0 1 10 17c-4.257 0-7.893-2.66-9.336-6.41ZM14 10a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" clip-rule="evenodd"></path>
                                    </svg>
                                    0
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                <div class="flex items-center justify-center">
                                    <svg x-tooltip.raw="This page was submitted for indexing on Jul 15, 2024" class="w-6 h-6 mr-2 cursor-pointer text-amber-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                <span class="text-gray-600">
                                    Jul 15, 2024
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-md text-gray-500 hidden md:table-cell">
                                <div class="flex justify-left">
                                    <a href="https://urlmonitor.com/sites/31812/sitemaps">
                                        <svg class="w-6 h-6 mr-2 cursor-pointer text-green-500" x-tooltip.raw="Included in the following sitemap: https://hrefcreative.com/sitemap_index.xml" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                            <path fill="currentColor" d="M472,328H448V264a24.027,24.027,0,0,0-24-24H272V176h32a24.028,24.028,0,0,0,24-24V80a24.028,24.028,0,0,0-24-24H208a24.028,24.028,0,0,0-24,24v72a24.028,24.028,0,0,0,24,24h32v64H88a24.027,24.027,0,0,0-24,24v64H40a24.028,24.028,0,0,0-24,24v72a24.028,24.028,0,0,0,24,24h80a24.028,24.028,0,0,0,24-24V352a24.028,24.028,0,0,0-24-24H96V272H240v56H216a24.028,24.028,0,0,0-24,24v72a24.028,24.028,0,0,0,24,24h80a24.028,24.028,0,0,0,24-24V352a24.028,24.028,0,0,0-24-24H272V272H416v56H392a24.028,24.028,0,0,0-24,24v72a24.028,24.028,0,0,0,24,24h80a24.028,24.028,0,0,0,24-24V352A24.028,24.028,0,0,0,472,328ZM216,88h80v56H216ZM112,360v56H48V360Zm176,0v56H224V360Zm176,56H400V360h64Z"></path>
                                        </svg>
                                    </a>
                                    <svg x-tooltip.raw="This page was last checked on Jul 18, 2024" class="w-6 h-6 mr-2 cursor-pointer text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                        <path d="M8 10a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0Z"></path>
                                        <path fill-rule="evenodd" d="M4.5 2A1.5 1.5 0 0 0 3 3.5v13A1.5 1.5 0 0 0 4.5 18h11a1.5 1.5 0 0 0 1.5-1.5V7.621a1.5 1.5 0 0 0-.44-1.06l-4.12-4.122A1.5 1.5 0 0 0 11.378 2H4.5Zm5 5a3 3 0 1 0 1.524 5.585l1.196 1.195a.75.75 0 1 0 1.06-1.06l-1.195-1.196A3 3 0 0 0 9.5 7Z" clip-rule="evenodd"></path>
                                    </svg>
                                    <svg x-tooltip.raw="This page was last crawled on Jul 15, 2024" class="w-6 h-6 mr-2 cursor-pointer text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                        <path fill-rule="evenodd" d="M6.56 1.14a.75.75 0 0 1 .177 1.045 3.989 3.989 0 0 0-.464.86c.185.17.382.329.59.473A3.993 3.993 0 0 1 10 2c1.272 0 2.405.594 3.137 1.518.208-.144.405-.302.59-.473a3.989 3.989 0 0 0-.464-.86.75.75 0 0 1 1.222-.869c.369.519.65 1.105.822 1.736a.75.75 0 0 1-.174.707 7.03 7.03 0 0 1-1.299 1.098A4 4 0 0 1 14 6c0 .52-.301.963-.723 1.187a6.961 6.961 0 0 1-1.158.486c.13.208.231.436.296.679 1.413-.174 2.779-.5 4.081-.96a19.655 19.655 0 0 0-.09-2.319.75.75 0 1 1 1.493-.146 21.239 21.239 0 0 1 .08 3.028.75.75 0 0 1-.482.667 20.873 20.873 0 0 1-5.153 1.249 2.521 2.521 0 0 1-.107.247 20.945 20.945 0 0 1 5.252 1.257.75.75 0 0 1 .482.74 20.945 20.945 0 0 1-.908 5.107.75.75 0 0 1-1.433-.444c.415-1.34.69-2.743.806-4.191-.495-.173-1-.327-1.512-.46.05.284.076.575.076.873 0 1.814-.517 3.312-1.426 4.37A4.639 4.639 0 0 1 10 19a4.639 4.639 0 0 1-3.574-1.63C5.516 16.311 5 14.813 5 13c0-.298.026-.59.076-.873-.513.133-1.017.287-1.512.46.116 1.448.39 2.85.806 4.191a.75.75 0 1 1-1.433.444 20.94 20.94 0 0 1-.908-5.107.75.75 0 0 1 .482-.74 20.838 20.838 0 0 1 5.252-1.257 2.493 2.493 0 0 1-.107-.247 20.874 20.874 0 0 1-5.153-1.249.75.75 0 0 1-.482-.667 21.342 21.342 0 0 1 .08-3.028.75.75 0 1 1 1.493.146 19.745 19.745 0 0 0-.09 2.319c1.302.46 2.668.786 4.08.96.066-.243.166-.471.297-.679a6.962 6.962 0 0 1-1.158-.486A1.348 1.348 0 0 1 6 6a4 4 0 0 1 .166-1.143 7.032 7.032 0 0 1-1.3-1.098.75.75 0 0 1-.173-.707 5.48 5.48 0 0 1 .822-1.736.75.75 0 0 1 1.046-.177Z" clip-rule="evenodd"></path>
                                    </svg>
                                    <svg x-tooltip.raw="The user and Google selected canonical URLs are both https://hrefcreative.com/personal-branding-real-estate-agents-tips-best-practices/." class="w-6 h-6 mr-2 cursor-pointer text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                        <path d="M12.232 4.232a2.5 2.5 0 0 1 3.536 3.536l-1.225 1.224a.75.75 0 0 0 1.061 1.06l1.224-1.224a4 4 0 0 0-5.656-5.656l-3 3a4 4 0 0 0 .225 5.865.75.75 0 0 0 .977-1.138 2.5 2.5 0 0 1-.142-3.667l3-3Z"></path>
                                        <path d="M11.603 7.963a.75.75 0 0 0-.977 1.138 2.5 2.5 0 0 1 .142 3.667l-3 3a2.5 2.5 0 0 1-3.536-3.536l1.225-1.224a.75.75 0 0 0-1.061-1.06l-1.224 1.224a4 4 0 1 0 5.656 5.656l3-3a4 4 0 0 0-.225-5.865Z"></path>
                                    </svg>
                                    <svg x-tooltip.raw="This page is allowed to be indexed in Google." class="w-6 h-6 mr-2 cursor-pointer text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                        <path fill-rule="evenodd" d="M10 1c3.866 0 7 1.79 7 4s-3.134 4-7 4-7-1.79-7-4 3.134-4 7-4Zm5.694 8.13c.464-.264.91-.583 1.306-.952V10c0 2.21-3.134 4-7 4s-7-1.79-7-4V8.178c.396.37.842.688 1.306.953C5.838 10.006 7.854 10.5 10 10.5s4.162-.494 5.694-1.37ZM3 13.179V15c0 2.21 3.134 4 7 4s7-1.79 7-4v-1.822c-.396.37-.842.688-1.306.953-1.532.875-3.548 1.369-5.694 1.369s-4.162-.494-5.694-1.37A7.009 7.009 0 0 1 3 13.179Z" clip-rule="evenodd"></path>
                                    </svg>
                                    <svg x-tooltip.raw="This page has been successfully fetched by Google." class="w-6 h-6 mr-2 cursor-pointer text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                        <path fill-rule="evenodd" d="M8 1a.75.75 0 0 1 .75.75V6h-1.5V1.75A.75.75 0 0 1 8 1Zm-.75 5v3.296l-.943-1.048a.75.75 0 1 0-1.114 1.004l2.25 2.5a.75.75 0 0 0 1.114 0l2.25-2.5a.75.75 0 0 0-1.114-1.004L8.75 9.296V6h2A2.25 2.25 0 0 1 13 8.25v4.5A2.25 2.25 0 0 1 10.75 15h-5.5A2.25 2.25 0 0 1 3 12.75v-4.5A2.25 2.25 0 0 1 5.25 6h2ZM7 16.75v-.25h3.75a3.75 3.75 0 0 0 3.75-3.75V10h.25A2.25 2.25 0 0 1 17 12.25v4.5A2.25 2.25 0 0 1 14.75 19h-5.5A2.25 2.25 0 0 1 7 16.75Z" clip-rule="evenodd"></path>
                                    </svg>
                                    <a href="https://search.google.com/search-console/inspect?resource_id=https://hrefcreative.com/&id=QuGr4woKUv42h--SxtDVaA&utm_medium=link&utm_source=api" target="_blank" class="cursor-pointer self-end">
                                        <svg class="w-6 h-6 cursor-pointer text-gray-500 hover:text-gray-700" x-tooltip.raw="Inspect URL in Google Search Console" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">
                                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"></path>
                                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"></path>
                                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"></path>
                                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"></path>
                                            <path d="M1 1h22v22H1z" fill="none"></path>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>