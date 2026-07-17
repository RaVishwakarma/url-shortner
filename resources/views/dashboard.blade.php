<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Dashboard
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

                @if(auth()->user()->role === 'super_admin')

                    <hr class="my-5">


                    <h4 class="text-lg font-semibold">
                        Invite Admin & Create Company
                    </h4>


                    <form method="POST" action="{{ route('invitations.store') }}" class="mt-4">

                        @csrf


                        <div>
                            <label>
                                Company Name
                            </label>

                            <input 
                                type="text"
                                name="company_name"
                                class="border rounded p-2 w-full"
                                required
                            >
                        </div>



                        <div class="mt-3">

                            <label>
                                Admin Email
                            </label>

                            <input 
                                type="email"
                                name="email"
                                class="border rounded p-2 w-full"
                                required
                            >

                        </div>



                        <input 
                            type="hidden"
                            name="role"
                            value="admin"
                        >


                        <button 
                            class="mt-4 bg-blue-600 text-white px-4 py-2 rounded"
                        >
                            Invite Admin
                        </button>


                    </form>

                @elseif(auth()->user()->role === 'admin')
                    <hr class="my-5">

                    <h4 class="text-lg font-semibold">Invite a User</h4>

                    <form method="POST" action="{{ route('invitations.store') }}" class="mt-4">
                        @csrf

                        <div>
                            <label for="email">Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required>
                        </div>

                        <div class="mt-3">
                            <label for="role">Role</label>
                            <select id="role" name="role" required>
                                <option value="admin">Admin</option>
                                <option value="member">Member</option>
                            </select>
                        </div>

                        <button class="mt-4 bg-blue-600 text-white px-4 py-2 rounded">
                            Send Invitation
                        </button>
                    </form>
                @endif

                @if($errors->any())
                    <ul class="mt-4 text-red-600">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif

                <a
                    href="{{ route('short-urls.index') }}"
                    class="inline-block mt-5 bg-blue-600 text-white px-4 py-2 rounded"
                >
                    {{ auth()->user()->role === 'super_admin' ? 'View All Short URLs' : 'Manage Short URLs' }}
                </a>
            </div>


        </div>

    </div>


</x-app-layout>
