<aside id="logo-sidebar"
    class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0"
    aria-label="Sidebar">
    <div class="h-full px-4 pb-6 overflow-y-auto bg-white">
        <ul class="space-y-2 text-sm font-medium text-gray-700">

            {{-- Section: Guards --}}
            <li>
                <button type="button"
                    class="flex items-center w-full px-2 py-2 text-left rounded-lg hover:bg-gray-100 transition group"
                    aria-controls="dropdown-guards" data-collapse-toggle="dropdown-guards">
                    <svg class="w-5 h-5 text-gray-500 group-hover:text-indigo-600" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H4Zm10 5a1 1 0 0 1 1-1h3a1 1 0 1 1 0 2h-3a1 1 0 0 1-1-1Zm-8 0a3 3 0 1 1 6 0 3 3 0 0 1-6 0Zm1.9 4a3 3 0 0 0-2.84 2.05l-.05.15c-.03.09-.04.12-.03.15.01.02.03.04.07.07a1 1 0 0 0 .31.28H12a1 1 0 0 0 .81-.41l.07-.07c.01-.03 0-.06-.03-.15l-.04-.12A3 3 0 0 0 10.1 13H7.9Z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="ml-3 flex-1">Guards</span>
                    <svg class="w-3 h-3 ml-auto text-gray-400 group-hover:text-indigo-600" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M1 1l4 4 4-4" />
                    </svg>
                </button>
                <ul id="dropdown-guards" class="hidden ml-7 mt-1 space-y-1">
                    <li>
                        <a href="{{ route('employees.index') }}"
                            class="block px-2 py-1 rounded hover:bg-gray-100 transition">All Guards</a>
                    </li>
                </ul>
            </li>

            {{-- Section: Sites --}}
            <li>
                <button type="button"
                    class="flex items-center w-full px-2 py-2 text-left rounded-lg hover:bg-gray-100 transition group"
                    aria-controls="dropdown-sites" data-collapse-toggle="dropdown-sites">
                    <svg class="w-5 h-5 text-gray-500 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-width="2" d="M3 21h18M4 18h16M6 10v8m4-8v8m4-8v8m4-8v8M4 9.5v-.95a1 1 0 0 1 .45-.85l7-4.5a1 1 0 0 1 1.1 0l7 4.5a1 1 0 0 1 .45.85v.95a.5.5 0 0 1-.5.5h-15a.5.5 0 0 1-.5-.5Z"/>
                    </svg>
                    <span class="ml-3 flex-1">Sites</span>
                    <svg class="w-3 h-3 ml-auto text-gray-400 group-hover:text-indigo-600" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M1 1l4 4 4-4" />
                    </svg>
                </button>
                <ul id="dropdown-sites" class="hidden ml-7 mt-1 space-y-1">
                    <li>
                        <a href="{{ route('sites.index') }}"
                            class="block px-2 py-1 rounded hover:bg-gray-100 transition">All Sites</a>
                    </li>
                </ul>
            </li>

            {{-- Section: Attendance --}}
            <li>
                <button type="button"
                    class="flex items-center w-full px-2 py-2 text-left rounded-lg hover:bg-gray-100 transition group"
                    aria-controls="dropdown-attendance" data-collapse-toggle="dropdown-attendance">
                    <svg class="w-5 h-5 text-gray-500 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 6H5m2 3H5m2 3H5m2 3H5m2 3H5m11-1a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2M7 3h11a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Zm8 7a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z"/>
                    </svg>
                    <span class="ml-3 flex-1">Attendance</span>
                    <svg class="w-3 h-3 ml-auto text-gray-400 group-hover:text-indigo-600" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M1 1l4 4 4-4" />
                    </svg>
                </button>
                <ul id="dropdown-attendance" class="hidden ml-7 mt-1 space-y-1">
                    <li><a href="{{ route('attendances.index') }}" class="block px-2 py-1 rounded hover:bg-gray-100 transition">All Attendances</a></li>
                    <li><a href="{{ route('attendances.site-entry') }}" class="block px-2 py-1 rounded hover:bg-gray-100 transition">Mark Attendance</a></li>
                </ul>
            </li>

            {{-- Section: Salaries --}}
            <li>
                <button type="button"
                    class="flex items-center w-full px-2 py-2 text-left rounded-lg hover:bg-gray-100 transition group"
                    aria-controls="dropdown-salaries" data-collapse-toggle="dropdown-salaries">
                    <svg class="w-5 h-5 text-gray-500 group-hover:text-indigo-600" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M7 6a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2h-2v-4a3 3 0 0 0-3-3H7V6Z" clip-rule="evenodd"/>
                        <path fill-rule="evenodd" d="M2 11a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-7Zm7.5 1a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5Z" clip-rule="evenodd"/>
                        <path d="M10.5 14.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z"/>
                    </svg>
                    <span class="ml-3 flex-1">Salaries</span>
                    <svg class="w-3 h-3 ml-auto text-gray-400 group-hover:text-indigo-600" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M1 1l4 4 4-4" />
                    </svg>
                </button>
                <ul id="dropdown-salaries" class="hidden ml-7 mt-1 space-y-1">
                    <li><a href="{{ route('salary.advance') }}" class="block px-2 py-1 rounded hover:bg-gray-100 transition">Salary Advances</a></li>
                    <li><a href="{{ route('salaries') }}" class="block px-2 py-1 rounded hover:bg-gray-100 transition">Salaries</a></li>
                </ul>
            </li>

            {{-- Section: Users --}}
            <li>
                <button type="button"
                    class="flex items-center w-full px-2 py-2 text-left rounded-lg hover:bg-gray-100 transition group"
                    aria-controls="dropdown-users" data-collapse-toggle="dropdown-users">
                    <svg class="w-5 h-5 text-gray-500 group-hover:text-indigo-600" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M12 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm-2 9a4 4 0 0 0-4 4v1a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-1a4 4 0 0 0-4-4h-4Z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ml-3 flex-1">Users</span>
                    <svg class="w-3 h-3 ml-auto text-gray-400 group-hover:text-indigo-600" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M1 1l4 4 4-4" />
                    </svg>
                </button>
                <ul id="dropdown-users" class="hidden ml-7 mt-1 space-y-1">
                    <li><a href="{{ route('users.index') }}" class="block px-2 py-1 rounded hover:bg-gray-100 transition">All Users</a></li>
                </ul>
            </li>
        </ul>
        <div class="absolute bottom-4 left-0 w-full px-4">
            <p class="text-xs text-gray-400 text-center">
                &copy; {{ now()->year }} <a class="text-purple-600 hover:font-bold" target="blank" href="https://wordroids.com">Wordroids</a>. All rights reserved.
            </p>
        </div>
    </div>
</aside>
