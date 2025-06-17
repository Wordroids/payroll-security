<x-app-layout>
    <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-base font-semibold text-gray-900">Salaries</h1>
                   
                </div>
                
            </div>

            <div class="mt-8 flow-root">
                <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                        <div class="h-[600px] overflow-x-scroll shadow-sm ring-1 ring-black/5 sm:rounded-lg">
                            <table class="min-w-full h-[250px] divide-y divide-gray-300">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-gray-900 sm:pl-6">Emp No</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Name</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Rank</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Address</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">NIC</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Date of Birth</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Date of Hire</th>
                                        <th class="relative py-3.5 pr-4 pl-3 sm:pr-6">
                                            <span class="sr-only">Edit</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @forelse ($employees as $employee)
                                        <tr>
                                            <td class="py-4 pr-3 pl-4 text-sm font-medium text-gray-900 whitespace-nowrap sm:pl-6">
                                                {{ $employee->emp_no }}
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                {{ $employee->name }}
                                            </td>
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
                                            <td class="relative py-4 pr-4 pl-3 text-right text-sm font-medium whitespace-nowrap sm:pr-6">
                                                <a href="{{ route('salaries.show', $employee->id) }}"
                                                   class="text-indigo-600 hover:text-indigo-900 mr-4">View Salary Log</a>
                                            
                                                
                                            </td>
                                            
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
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
</x-app-layout>
