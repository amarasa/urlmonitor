<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ env('APP_NAME') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div>
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex justify-between items-center mb-4">
                        <div class="inline-flex items-center gap-2">
                            @php
                            $cleanedUrl = \Illuminate\Support\Str::replaceFirst('http://', '', $site->site_url);
                            $cleanedUrl = \Illuminate\Support\Str::replaceFirst('https://', '', $cleanedUrl);
                            $cleanedUrl = rtrim($cleanedUrl, '/');
                            @endphp
                            <h2 class="text-3xl font-bold leading-tight tracking-tight text-gray-900 inline-flex items-center">
                                <a href="{{ route('dashboard.sites') }}" class="inline-flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="h-4 w-4 mr-2"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                        <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z" />
                                    </svg>
                                </a>
                                {{ $cleanedUrl }}
                            </h2>

                            <button id="refresh-btn" data-tooltip="Refresh the list of sitemaps from Search Console." class="bg-gray-100 hover:bg-gray-200 p-2 inline-flex items-center rounded text-gray-500 hover:text-gray-700 text-sm" onclick="refreshSitemaps({{ $site->id }})">
                                <svg id="refresh-icon" class="text-indigo-400 mr-1" wire:loading.class="animate-spin" wire:target="refreshGSCData" width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 3 16.5 3 16.5M2 12C2 6.47715 6.44444 2 12 2C18.6667 2 22 7.5 22 7.5M22 7.5V4M22 7.5H18.5M3 16.5H6.5M3 16.5V20" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                                Refresh
                            </button>

                        </div>
                    </div>
                    <h2 class="text-md font-medium text-gray-500 mt-4 mb-8">
                        Use this page to view and manage the sitemaps that have been submitted to your Google Search Console account.
                    </h2>
                    <div class="overflow-x-auto">

                        <table class="table-fixed min-w-full divide-y divide-gray-100">
                            <thead class="bg-black">
                                <tr class="text-left text-sm font-semibold text-gray-50 tracking-wider w-1/3">
                                    <th class="py-2 px-4 text-left rounded-tl-lg">Sitemap</th>
                                    <th class="text-center px-6 py-4 text-left text-sm font-semibold text-gray-50 tracking-wider">URLs <svg data-tooltip="The number of URLs in this sitemap according to Google." class="w-4 h-4 ml-1 inline cursor-pointer" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z" clip-rule="evenodd"></path>
                                        </svg></th>
                                    <th class="text-center px-6 py-4 text-left text-sm font-semibold text-gray-50 tracking-wider">Index <svg data-tooltip="Is this a sitemap index?" class="w-4 h-4 ml-1 inline cursor-pointer" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z" clip-rule="evenodd"></path>
                                        </svg></th>
                                    <th class="text-center py-2 px-4 text-left rounded-tr-lg">Enabled? <svg data-tooltip="Is this sitemap enabled for us to automatically submit URLs for indexing?" class="w-4 h-4 ml-1 inline cursor-pointer" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z" clip-rule="evenodd"></path>
                                        </svg></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach ($sitemaps as $sitemap)
                                <tr class="border-t border-gray-200">
                                    <td class="py-2 px-4">
                                        <a href="{{ $sitemap->url }}" target="_blank" class="text-blue-500 hover:underline">
                                            {{ $sitemap->url }}
                                        </a>
                                        <div class="mt-1">
                                            <span class="inline-flex items-center bg-pink-200 text-pink-700 text-xs font-medium mr-2 px-2.5 py-0.5 rounded-full cursor-pointer" x-tooltip.raw="This sitemap has 0 errors. Visit your Google Search Console account to view the errors.">
                                                <span class="w-2 h-2 mr-1 bg-pink-500 rounded-full"></span>
                                                {{ $sitemap->errors }} errors
                                            </span>
                                            <span class="inline-flex items-center bg-amber-200 text-amber-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded-full cursor-pointer" x-tooltip.raw="This sitemap has 3 warnings. Visit your Google Search Console account to view the warnings.">
                                                <span class="w-2 h-2 mr-1 bg-amber-500 rounded-full"></span>
                                                {{ $sitemap->warnings }} warnings
                                            </span>
                                        </div>
                                        <div class="mt-1">
                                            <p class="mt-3 text-xs leading-5 font-normal text-gray-500">
                                                Last downloaded on {{ \Carbon\Carbon::parse($sitemap->last_downloaded)->format('M j, Y') }}
                                            </p>
                                            <p class="mt-2 text-xs leading-5 font-normal text-gray-500">
                                                Last submitted on {{ \Carbon\Carbon::parse($sitemap->last_submitted)->format('M j, Y') }}
                                            </p>
                                        </div>
                                    </td>
                                    <td class="py-2 px-4 text-center">
                                        {{ $sitemap->number_of_urls }}
                                    </td>
                                    <td class="py-2 px-4 text-center">
                                        {{ $sitemap->is_index ? 'Yes' : 'No' }}
                                    </td>
                                    <td class="py-2 px-4 text-center">
                                        <!-- Toggle switch for enabled status -->
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="sr-only peer" {{ $sitemap->enabled ? 'checked' : '' }}>
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                        </label>
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
    <script>
        function refreshSitemaps(siteId) {
            const refreshIcon = document.getElementById('refresh-icon');
            refreshIcon.classList.add('spin');

            fetch(`/dashboard/sites/${siteId}/refresh-sitemaps`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Failed to refresh sitemaps.');
                    }
                    refreshIcon.classList.remove('spin');
                })
                .catch(error => {
                    console.error('Error:', error);
                    refreshIcon.classList.remove('spin');
                });
        }
    </script>
    <style>
        .spin {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</x-app-layout>