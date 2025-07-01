<x-app-layout>
    <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg">
        <div class="px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h1 class="text-base font-semibold text-gray-900 mb-4">Salary Overview</h1>

            <form method="GET" action="{{ route('salaries.overview') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="month" class="block text-sm font-medium text-gray-700">Month</label>
                    <input type="month" name="month" id="month" value="{{ $month }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label for="employee_id" class="block text-sm font-medium text-gray-700">Employee</label>
                    <select name="employee_id" id="employee_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">All Employees</option>
                        @foreach($allEmployees as $emp)
                        <option value="{{ $emp->id }}" {{ $selectedEmployee == $emp->id ? 'selected' : '' }}>
                            {{ $emp->name }} ({{ $emp->emp_no }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-500 focus:outline-none">
                        Filter
                    </button>
                </div>
            </form>
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
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Shifts</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Basic</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">BR Allow</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">OT Earnings</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Att. Bonus</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Other Allow</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Sub Total</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Gross Pay</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">EPF 8%</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Salary Adv</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Meals</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Uniform</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Total Deduct</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Net Pay</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($salaryData as $data)
                        <tr>
                            <td class="py-4 pr-3 pl-4 text-sm font-medium text-gray-900 whitespace-nowrap sm:pl-6">
                                {{ $data['employee']->emp_no }}
                            </td>
                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ $data['employee']->name }}
                            </td>
                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ number_format($data['total_shifts'], 2) }}
                            </td>
                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ number_format($data['basic'], 2) }}
                            </td>
                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ number_format($data['br_allow'], 2) }}
                            </td>
                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ number_format($data['ot_earnings'], 2) }}
                            </td>
                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ number_format($data['attendance_bonus'], 2) }}
                            </td>
                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ number_format($data['other_allowances'], 2) }}
                            </td>
                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ number_format($data['sub_total'], 2) }}
                            </td>
                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ number_format($data['gross_pay'], 2) }}
                            </td>
                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ number_format($data['epf_employee'], 2) }}
                            </td>
                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ number_format($data['salary_advance'], 2) }}
                            </td>
                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ number_format($data['meal_deductions'], 2) }}
                            </td>
                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ number_format($data['uniform_deductions'], 2) }}
                            </td>
                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ number_format($data['total_deductions'], 2) }}
                            </td>
                            <td class="px-3 py-4 text-sm font-medium text-green-600 whitespace-nowrap">
                                {{ number_format($data['net_pay'], 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="16" class="px-6 py-4 text-center text-sm text-gray-500">
                                No salary data found for the selected filters.
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
