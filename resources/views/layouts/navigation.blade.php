<nav class="border-b border-gray-100 bg-white dark:border-gray-700 dark:bg-gray-800">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex min-h-16 flex-wrap items-center justify-between gap-3 py-3">
            <div class="flex items-center gap-6">
                <a href="{{ route('dashboard') }}">
                    <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                </a>

                <a class="text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white" href="{{ route('dashboard') }}">
                    Dashboard
                </a>
            </div>

            <div class="flex items-center gap-3 text-sm">
                <span class="text-gray-600 dark:text-gray-300">{{ auth()->user()->name }}</span>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button class="rounded-md bg-gray-800 px-3 py-2 font-semibold text-white hover:bg-gray-700 dark:bg-gray-200 dark:text-gray-800 dark:hover:bg-white" type="submit">
                        Log out
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
