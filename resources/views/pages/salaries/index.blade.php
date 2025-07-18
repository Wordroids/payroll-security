<x-app-layout>
    <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-base font-semibold text-gray-900">Salaries</h1>
                </div>
            </div>

            <div class="mt-8"  style="height: calc(100vh - 170px); display: flex; flex-direction: column;">
                <div class="flex-1 overflow-auto relative">
                    <div class="absolute top-0 left-0 right-0 bottom-0 overflow-auto">
                        <div class="inline-flex min-w-max">
                            <!-- Fixed columns (Emp No and Name) -->
                            <div class="sticky left-0 z-20 bg-white">
                                <table class="min-w-full divide-y divide-gray-300">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-gray-900 sm:pl-6 sticky top-0 z-30 bg-gray-50">Emp No</th>
                                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 sticky top-0 z-30 bg-gray-50">Name</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        @forelse ($employees as $employee)
                                            <tr>
                                                <td class="py-4 pr-3 pl-4 text-sm font-medium text-gray-900 whitespace-nowrap sm:pl-6 bg-white">
                                                    {{ $employee->emp_no }}
                                                </td>
                                                <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap bg-white">
                                                    {{ $employee->name }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="px-6 py-4 text-center text-sm text-gray-500 bg-white">
                                                    No employees found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Scrollable columns -->
                            <div>
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 sticky top-0 z-10 bg-gray-50">Rank</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 sticky top-0 z-10 bg-gray-50">Address</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 sticky top-0 z-10 bg-gray-50">NIC</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 sticky top-0 z-10 bg-gray-50">Date of Birth</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 sticky top-0 z-10 bg-gray-50">Date of Hire</th>
                                        <th class=" py-3.5 pr-4 pl-3 text-left text-sm font-semibold text-gray-900 sticky top-0 z-10 bg-gray-50 sm:pr-6">
                                            <span>Action</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @forelse ($employees as $employee)
                                        <tr>
                                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                {{ $employee->rank }}
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                {{ $employee->address }}
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                {{ $employee->nic }}
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                {{ \Carbon\Carbon::parse($employee->date_of_birth)->format('Y-m-d') }}
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                {{ \Carbon\Carbon::parse($employee->date_of_hire)->format('Y-m-d') }}
                                            </td>
                                            <td class="relative py-4 pr-4 pl-3 text-left text-sm font-medium whitespace-nowrap sm:pr-6">
                                                <a href="{{ route('salaries.show', $employee->id) }}"
                                                   class="text-indigo-600 hover:text-indigo-900">View Salary Log</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                                No employees found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
