<x-app-layout>
    <div class="max-w-5xl mx-auto px-6 py-10">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Salary Overview</h1>
            <p class="text-gray-500 text-sm mt-1">Detailed salary breakdown for <strong>{{ $employee->name }}</strong></p>
        </div>

        {{-- Employee Info --}}
        <div class="bg-white rounded-xl shadow p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Employee Information</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-gray-600">
                <div><strong>Emp No:</strong> {{ $employee->emp_no }}</div>
                <div><strong>Name:</strong> {{ $employee->name }}</div>
                <div><strong>Phone:</strong> {{ $employee->phone }}</div>
                <div><strong>NIC:</strong> {{ $employee->nic }}</div>
                <div><strong>Rank:</strong> {{ $employee->rank }}</div>
                <div><strong>Date of Birth:</strong> {{ \Carbon\Carbon::parse($employee->date_of_birth)->format('Y-m-d') }}</div>
                <div><strong>Date of Hire:</strong> {{ \Carbon\Carbon::parse($employee->date_of_hire)->format('Y-m-d') }}</div>
                <div><strong>Address:</strong> {{ $employee->address }}</div>
            </div>
        </div>

        {{-- Month Filter --}}
        <form method="GET" action="{{ route('salaries.show', $employee->id) }}" class="mb-10">
            <div class="flex flex-col sm:flex-row sm:items-end gap-4">
                <div>
                    <label for="month" class="block text-sm font-medium text-gray-700">Select Month</label>
                    <input type="month" name="month" id="month" value="{{ $month }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div>
                    <button type="submit"
                        class="inline-flex mt-1 sm:mt-0 items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-500 focus:outline-none">
                        Filter
                    </button>
                </div>
            </div>
        </form>

        {{-- Shift Breakdown --}}
        <div class="bg-white rounded-xl shadow p-6 mb-10">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Assigned Shift Locations</h2>

            @if ($siteSummaries)
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left text-gray-600">
                        <thead class="bg-gray-50 font-semibold text-gray-700 border-b">
                            <tr>
                                <th class="px-4 py-2">Location</th>
                                <th class="px-4 py-2">No. of Shifts</th>
                                <th class="px-4 py-2">Shift Rate</th>
                                <th class="px-4 py-2">Earnings</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($siteSummaries as $summary)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-2">{{ $summary['site']->name }} - {{ $summary['site']->location }}</td>
                                    <td class="px-4 py-2">{{ number_format($summary['shifts'], 2) }}</td>
                                    <td class="px-4 py-2">Rs. {{ number_format($summary['site']->guard_shift_rate, 2) }}</td>
                                    <td class="px-4 py-2">Rs. {{ number_format($summary['earning'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-sm mt-2">No shift locations assigned.</p>
            @endif
        </div>

        {{-- Salary Breakdown --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Salary Breakdown</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-gray-700">
                <div><strong>Total Shift Earnings:</strong> Rs. {{ number_format($totalShiftEarning, 2) }}</div>
                <div><strong>Total OT Hours:</strong> {{ $totalOTHours }} hrs</div>
                <div><strong>OT Rate:</strong> Rs. {{ number_format($otRate, 2) }} / hr</div>
                <div><strong>OT Earnings:</strong> Rs. {{ number_format($otEarnings, 2) }}</div>
                <div><strong>Attendance Bonus:</strong> Rs. {{ number_format($employee->attendance_bonus, 2) }}</div>
                <div><strong>Basic Salary:</strong> Rs. {{ number_format($employee->basic, 2) }}</div>
                <div><strong>BR Allowance:</strong> Rs. {{ number_format($employee->br_allow, 2) }}</div>
                <div><strong>Other Allowances:</strong> Rs. {{ number_format($otherAllowances, 2) }}</div>
                <div><strong>Sub Total:</strong> Rs. {{ number_format($subTotal, 2) }}</div>
                <div><strong>Gross Pay:</strong> Rs. {{ number_format($grossPay, 2) }}</div>
                <div><strong>EPF (8%):</strong> Rs. {{ number_format($epfEmployee, 2) }}</div>
                <div><strong>Salary Advances:</strong> Rs. {{ number_format($employee->totalSalaryAdvance, 2) }}</div>
                <div><strong>Total Deductions:</strong> Rs. {{ number_format($totalDeductions, 2) }}</div>
                <div class="text-green-700 font-semibold"><strong>Net Pay:</strong> Rs. {{ number_format($totalEarnings, 2) }}</div>
                <div class="text-blue-700"><strong>EPF/ETF (Employer 15%):</strong> Rs. {{ number_format($epfEtfEmployer, 2) }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
