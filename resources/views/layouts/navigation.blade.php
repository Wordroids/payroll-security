

<nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200 shadow-sm">
    
    <div class="flex items-center justify-between px-4 py-3 lg:px-6">
        {{-- Left Section --}}
        <div class="flex items-center">
            {{-- Mobile Toggle Button --}}
            <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar"
                aria-controls="logo-sidebar"
                type="button"
                class="inline-flex items-center p-2 text-gray-600 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                <span class="sr-only">Open sidebar</span>
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 5.25a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10zm.75 4.75a.75.75 0 000 1.5h7.5a.75.75 0 000-1.5h-7.5z" />
                </svg>
            </button>

            {{-- Logo + Brand --}}
            <a href="{{ route('dashboard') }}" class="flex items-center ms-3 space-x-2">
                <img src="{{ asset('images/logo.jpg') }}" alt="Logo" class="w-12 h-15">
                <span class="text-lg font-semibold text-gray-800 uppercase">Smart Syndicates</span>
            </a>
        </div>

        {{-- Right Section --}}
        <div class="relative">
            <button type="button"
                class="flex items-center space-x-2 focus:outline-none"
                data-dropdown-toggle="dropdown-user">
                <img class="w-9 h-9 rounded-full border border-gray-300"
                    src="https://flowbite.com/docs/images/people/profile-picture-5.jpg" alt="User Avatar">
                <span class="hidden sm:inline-block text-sm text-gray-700 font-medium">
                    {{ auth()->user()->name }}
                </span>
            </button>

            {{-- Dropdown Menu --}}
            <div id="dropdown-user"
                class="hidden absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-md z-50">
                <div class="px-4 py-3 text-blue-600 hover:bg-blue-50">
                    <p class="text-sm font-semibold text-gray-900"><a href="{{route('profile.edit')}}">
                        {{ auth()->user()->name }}</a> </p>
                    <p class="text-sm text-gray-600 truncate"><a href="{{route('profile.edit')}}">
                        {{ auth()->user()->email }}</a></p>
                </div>
                <div class="border-t border-gray-100">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                            Sign out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>
