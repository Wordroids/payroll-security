<x-app-layout>
    <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-base font-semibold text-gray-900">Salary Advances</h1>
                    <p class="mt-2 text-sm text-gray-700">
                        A list of all salary advances including employee details and advance data.
                    </p>
                </div>
                <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                    <a href="{{ route('salary.advance.create') }}"
                       class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        Add Salary Advance
                    </a>
                </div>
            </div>

            <div class="mt-8 flow-root">
                <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                        <div class="overflow-hidden shadow-sm ring-1 ring-black/5 sm:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-gray-900 sm:pl-6">Emp No</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Name</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Phone</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">NIC</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Advance Date</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Amount</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Reason</th>
                                        <th class="px-3 py-3.5 text-center text-sm font-semibold text-gray-900">Actions</th>
                                        
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @forelse ($salaryAdvances as $salaryAdvance)
                                        <tr>
                                            <td class="py-4 pr-3 pl-4 text-sm font-medium text-gray-900 whitespace-nowrap sm:pl-6">
                                                {{ $salaryAdvance->employee->emp_no ?? 'N/A' }}
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                {{ $salaryAdvance->employee->name ?? 'N/A' }}
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                {{ $salaryAdvance->employee->phone ?? 'N/A' }}
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                {{ $salaryAdvance->employee->nic ?? 'N/A' }}
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                {{ \Carbon\Carbon::parse($salaryAdvance->advance_date)->format('Y-m-d') }}
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                {{ number_format($salaryAdvance->amount, 2) }}
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                {{ $salaryAdvance->reason ?? '-' }}
                                            </td>
                                            <td class="relative py-4 pr-4 pl-3 text-right text-sm font-medium whitespace-nowrap sm:pr-6">
                                                <a href="{{ route('salary.advance.edit', $salaryAdvance->id) }}"
                                                   class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>

                                                <form action="{{ route('salary.advance.destroy', $salaryAdvance->id) }}" method="POST" class="inline-block"
                                                      onsubmit="return confirm('Are you sure you want to delete this salary advance?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                                No salary advances found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                
                            </table>
                        </div>

                        <!-- Employee-wise Summary -->
                        <div class="mt-16 overflow-hidden shadow-sm ring-1 ring-black/5 sm:rounded-lg">
                            <h2 class="px-6 py-3 bg-gray-50 text-left text-sm font-semibold text-gray-900">Employee-wise Advance Summary</h2>
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-gray-900 sm:pl-6">Emp No</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Name</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Total Advances</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @php
                                        $employeeSummary = $salaryAdvances->groupBy('employee_id')
                                            ->map(function($advances) {
                                                return [
                                                    'employee' => $advances->first()->employee,
                                                    'count' => $advances->count(),
                                                    'total' => $advances->sum('amount')
                                                ];
                                            });
                                    @endphp
                                    
                                    @forelse ($employeeSummary as $summary)
                                        <tr>
                                            <td class="py-4 pr-3 pl-4 text-sm font-medium text-gray-900 whitespace-nowrap sm:pl-6">
                                                {{ $summary['employee']->emp_no ?? 'N/A' }}
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                {{ $summary['employee']->name ?? 'N/A' }}
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                {{ $summary['count'] }}
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                {{ number_format($summary['total'], 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                                No summary data available.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td class="py-3.5 pl-4 pr-3 text-sm font-semibold text-gray-900 sm:pl-6">
                                            Total Employees: {{ count($employeeSummary) }}
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td class="py-3.5 px-3 text-sm font-semibold text-gray-900 text-right">
                                            Grand Amount: {{ number_format($salaryAdvances->sum('amount'), 2) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
