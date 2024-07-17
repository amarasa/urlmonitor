<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ env('APP_NAME') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        @if ($connected)
                        <div class="inline-flex items-center gap-2">
                            <h2 class="text-3xl font-bold leading-tight tracking-tight text-gray-900 inline-block">Your Sites</h2>
                            <button id="refresh-btn" data-tooltip="Refresh the list of sites from Search Console." class="bg-gray-100 hover:bg-gray-200 p-2 inline-flex items-center rounded text-gray-500 hover:text-gray-700 text-sm" onclick="refreshSites()">
                                <svg id="refresh-icon" class="text-indigo-400 mr-1" wire:loading.class="animate-spin" wire:target="refreshGSCData" width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 3 16.5 3 16.5M2 12C2 6.47715 6.44444 2 12 2C18.6667 2 22 7.5 22 7.5M22 7.5V4M22 7.5H18.5M3 16.5H6.5M3 16.5V20" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                                Refresh
                            </button>
                        </div>
                        @endif
                    </div>
                    @if (!$connected)
                    <div class="text-center">
                        <p>You are not connected to Google Search Console.</p>
                        <a href="{{ route('google.connect') }}" class="btn btn-primary">
                            Connect to Google Search Console
                        </a>
                    </div>
                    @else
                    <div class="overflow-x-auto">
                        <table class="table-fixed min-w-full divide-y divide-gray-100">
                            <thead class="bg-black">
                                <tr class="px-6 py-4 text-left text-sm font-semibold text-gray-50 tracking-wider w-1/3">
                                    <th class="py-2 px-4 text-left rounded-tl-lg">Website</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-50 tracking-wider w-1/3">Enabled</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-50 tracking-wider w-1/3">
                                        Grant Access
                                        <svg data-tooltip="To index your pages we need you to add us as an Owner to your site in Google Search Console." class="w-4 h-4 ml-1 inline cursor-pointer" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z" clip-rule="evenodd"></path>
                                        </svg>
                                    </th>
                                    <th class="py-2 px-4 rounded-tr-lg"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach ($sites as $site)
                                <tr class="border-t border-gray-200">

                                    <td class="py-2 px-4">
                                        {{ $site->site_url }}
                                        <div class="inline-flex items-center gap-2">
                                            @php
                                            $outerClass = 'bg-gray-100 text-gray-800';
                                            $innerClass = 'bg-gray-500';

                                            if ($site->permissions === 'Owner') {
                                            $outerClass = 'bg-green-100 text-green-800';
                                            $innerClass = 'bg-green-500';
                                            } elseif ($site->permissions === 'Unverified') {
                                            $outerClass = 'bg-red-100 text-red-800';
                                            $innerClass = 'bg-red-500';
                                            }

                                            $sitemapCount = $site->sitemaps->count();
                                            @endphp

                                            <span class="inline-flex items-center {{ $outerClass }} text-xs font-medium mr-2 px-2.5 py-0.5 rounded-full">
                                                <span class="w-2 h-2 mr-1 {{ $innerClass }} rounded-full"></span>
                                                {{$site->permissions}}
                                            </span>

                                            <a href="{{ url('/dashboard/sites/'.$site->id.'/sitemaps') }}" class="inline-flex items-center bg-gray-100 text-gray-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded-full hover:bg-gray-200 transition duration-300 ease-in-out">
                                                <span class="w-2 h-2 mr-1 bg-gray-700 rounded-full"></span>
                                                <span>{{ $sitemapCount }} {{ Str::plural('Sitemap', $sitemapCount) }}</span>
                                            </a>
                                        </div>
                                    </td>



                                    <td class="py-2 px-4">
                                        <!-- Toggle switch for enabled status -->
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                        </label>
                                    </td>
                                    <td class="py-2 px-4">
                                        <!-- Example button for granting access -->
                                        <button class="bg-blue-500 text-white px-4 py-1 rounded transition duration-300 ease-in-out hover:bg-blue-400">Grant</button>
                                    </td>
                                    <td class="py-2 px-4">
                                        <!-- Details button linking to site-specific page -->
                                        <a href="{{ url('/dashboard/sites/'.$site->id) }}" class="bg-green-500 text-white px-4 py-1 rounded inline-flex items-center transition duration-300 ease-in-out hover:bg-green-400">
                                            Details
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="h-4 w-4 ml-1" fill="white">
                                                <path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <!-- Apply rounded corners to the bottom left and bottom right of the table -->
                            <tfoot>
                                <tr>
                                    <td class="py-2 px-4 rounded-bl-lg"></td>
                                    <td></td>
                                    <td></td>
                                    <td class="py-2 px-4 rounded-br-lg"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function refreshSites() {
            const refreshIcon = document.getElementById('refresh-icon');
            refreshIcon.classList.add('spin');

            fetch('/auth/google/refresh')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Failed to refresh sites.');
                    }
                    refreshIcon.classList.remove('spin');
                })
                .catch(error => {
                    console.error('Error:', error);
                    refreshIcon.classList.remove('spin');
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            tippy('[data-tooltip]', {
                content: (reference) => reference.getAttribute('data-tooltip'),
                arrow: true,
                theme: 'light',
            });
        });
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