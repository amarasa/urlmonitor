<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Disconnect Google Search Console Connection') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Disconnecting your Google Search Console connection will remove access to your website\'s performance data within the app. This will have no impact on your website or your search console data.') }}
        </p>
    </header>

    <x-danger-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-disconnect-gsc')">{{ __('Disconnect') }}</x-danger-button>

    <x-modal name="confirm-disconnect-gsc" :show="$errors->disconnectGsc->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.disconnect-gsc') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Are you sure you want to disconnect your Google Search Console Connection?') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Disconnecting your Google Search Console connection will remove access to your website\'s performance data within the app. This will have no impact on your website or your search console data.') }}
            </p>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    {{ __('Disconnect') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>