<x-guest-layout>
    <form method="POST" action="{{ route('invitations.register', $invitation->token) }}" class="space-y-4">
        @csrf

        <div>
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                Create account
            </h2>

            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                You were invited to join {{ $invitation->company->name }}
                as {{ ucfirst($invitation->role) }}.
            </p>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                <span class="font-medium text-gray-700 dark:text-gray-300">Email:</span>
                {{ $invitation->email }}
            </p>
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input
                id="name"
                class="block mt-1 w-full"
                type="text"
                name="name"
                :value="old('name')"
                required
                autofocus
                autocomplete="name"
            />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input
                id="password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                required
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm password')" />
            <x-text-input
                id="password_confirmation"
                class="block mt-1 w-full"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end pt-2">
            <x-primary-button>
                Create account
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
