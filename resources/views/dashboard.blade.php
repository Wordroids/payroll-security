<x-app-layout>
    <div class="py-10 px-6 max-w-7xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Dashboard</h1>

        {{-- Top Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white shadow rounded-lg p-4">
                <h2 class="text-sm text-gray-500">Total Guards</h2>
                <p class="text-2xl font-bold text-indigo-600 mt-1">{{ $totalEmployees }}</p>
            </div>

            <div class="bg-white shadow rounded-lg p-4">
                <h2 class="text-sm text-gray-500">Total Sites</h2>
                <p class="text-2xl font-bold text-indigo-600 mt-1">{{ $totalSites }}</p>
            </div>

            <div class="bg-white shadow rounded-lg p-4">
                <h2 class="text-sm text-gray-500">Total Salary Last Month</h2>
                <p class="text-2xl font-bold text-indigo-600 mt-1">Rs.{{ number_format($lastMonthSalary, 2) }}</p>
            </div>

            <div class="bg-white shadow rounded-lg p-4">
                <h2 class="text-sm text-gray-500">Salary Advances of Last Month</h2>
                <p class="text-2xl font-bold text-indigo-600 mt-1">Rs. {{ number_format($lastMonthAdvance, 2) }}</p>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="mt-10">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Quick Actions</h2>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('employees.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded hover:bg-indigo-500">
                    View All Guards
                </a>
                <a href="{{ route('attendances.site-entry') }}"
                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded hover:bg-green-500">
                    Mark Attendance
                </a>
                <a href="{{ route('salaries') }}"
                   class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white text-sm font-medium rounded hover:bg-yellow-400">
                    Generate Salaries
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
