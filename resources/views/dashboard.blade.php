<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Sembark URL Shortner
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                    Welcome {{ auth()->user()->name }}
                </h3>
                <p class="mt-2 text-gray-700 dark:text-gray-300">
                    Role: {{ auth()->user()->role }}
                </p>

                @if(session('success'))
                    <p class="mt-4 text-green-600">{{ session('success') }}</p>
                @endif

                @if(session('invitation_url'))
                    <p class="mt-2">
                        Invitation link:
                        <a class="text-blue-600 underline" href="{{ session('invitation_url') }}">
                            {{ session('invitation_url') }}
                        </a>
                    </p>
                @endif

                @if(auth()->user()->role !== 'super_admin')
                    <section class="mt-6 border-t border-gray-200 pt-6 dark:border-gray-700">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Generate Short URL</h4>

                        <form method="POST" action="{{ route('short-urls.store') }}" class="mt-4 max-w-2xl">
                            @csrf

                            <div>
                                <x-input-label for="original_url" value="Original URL" />

                                <x-text-input
                                    id="original_url"
                                    name="original_url"
                                    type="url"
                                    class="mt-1 block w-full"
                                    :value="old('original_url')"
                                    placeholder="https://example.com"
                                    autocomplete="url"
                                    required
                                />

                                <x-input-error :messages="$errors->get('original_url')" class="mt-2" />
                            </div>

                            <x-primary-button class="mt-4">Create Short URL</x-primary-button>
                        </form>
                    </section>
                @endif

                <section class="mt-6 border-t border-gray-200 pt-6 dark:border-gray-700">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        @if(auth()->user()->role === 'super_admin')
                            All Short URLs
                        @elseif(auth()->user()->role === 'admin')
                            Company Short URLs
                        @else
                            Your Short URLs
                        @endif
                    </h4>

                    @if($urls->isNotEmpty())
                        <div class="mt-4 overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Original URL</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Short URL</th>
                                        @if(auth()->user()->role !== 'member')
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Company</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Created By</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                                    @foreach($urls as $url)
                                        <tr>
                                            <td class="max-w-xs break-all px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $url->original_url }}</td>
                                            <td class="px-4 py-3 text-sm">
                                                <a class="break-all text-blue-600 underline hover:text-blue-800 dark:text-blue-400" href="{{ route('short-urls.redirect', $url->short_code) }}">
                                                    {{ route('short-urls.redirect', $url->short_code) }}
                                                </a>
                                            </td>
                                            @if(auth()->user()->role !== 'member')
                                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $url->company->name }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $url->user->name }}</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="mt-4 text-sm text-gray-700 dark:text-gray-300">No short URLs found.</p>
                    @endif
                </section>

                @if(auth()->user()->role === 'super_admin')
                    <section class="mt-6 border-t border-gray-200 pt-6 dark:border-gray-700">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Invite Admin & Create Company</h4>

                        <form method="POST" action="{{ route('invitations.store') }}" class="mt-4 max-w-2xl">
                            @csrf

                            <div>
                                <x-input-label for="company_name" value="Company Name" />
                                <x-text-input id="company_name" name="company_name" type="text" class="mt-1 block w-full" :value="old('company_name')" required />
                            </div>

                            <div class="mt-3">
                                <x-input-label for="email" value="Admin Email" />
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" autocomplete="email" required />
                            </div>

                            <input type="hidden" name="role" value="admin">

                            <x-primary-button class="mt-4">Invite Admin</x-primary-button>
                        </form>
                    </section>
                @elseif(auth()->user()->role === 'admin')
                    <section class="mt-6 border-t border-gray-200 pt-6 dark:border-gray-700">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Invite a User</h4>

                        <form method="POST" action="{{ route('invitations.store') }}" class="mt-4 max-w-2xl">
                            @csrf

                            <div>
                                <x-input-label for="email" value="Email" />
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" autocomplete="email" required />
                            </div>

                            <div class="mt-3">
                                <x-input-label for="role" value="Role" />
                                <select id="role" name="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" required>
                                    <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                                    <option value="member" @selected(old('role') !== 'admin')>Member</option>
                                </select>
                            </div>

                            <x-primary-button class="mt-4">Send Invitation</x-primary-button>
                        </form>
                    </section>
                @endif

                @if($errors->any() && ! $errors->has('original_url'))
                    <ul class="mt-4 list-disc pl-5 text-sm text-red-600">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
