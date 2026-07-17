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



                @if(auth()->user()->role === 'super_admin')

                    <hr class="my-5">


                    <h4 class="text-lg font-semibold">
                        Invite Admin & Create Company
                    </h4>


                    <form method="POST" action="/invite" class="mt-4">

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


                @else


                    <a 
                        href="/short-urls"
                        class="inline-block mt-5 bg-blue-600 text-white px-4 py-2 rounded"
                    >
                        Manage Short URLs
                    </a>


                @endif


            </div>


        </div>

    </div>


</x-app-layout>