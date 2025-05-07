<x-app-layout>
    <div class="max-w-6xl mx-auto px-6 py-10 space-y-10">
        {{-- Header --}}
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Employee Salary Breakdown</h1>
            <p class="text-gray-500 text-sm mt-1">
                Detailed salary breakdown for <strong>{{ $employee->name }}</strong>
            </p>
        </div>

        {{-- Employee Info --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Employee Information</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-sm text-gray-700">
                <div><strong>Emp No:</strong> {{ $employee->emp_no }}</div>
                <div><strong>Name:</strong> {{ $employee->name }}</div>
                <div><strong>Phone:</strong> {{ $employee->phone }}</div>
                <div><strong>NIC:</strong> {{ $employee->nic }}</div>
                <div><strong>Rank:</strong> {{ $employee->rank }}</div>
                <div><strong>Date of Birth:</strong> {{ \Carbon\Carbon::parse($employee->date_of_birth)->format('Y-m-d') }}</div>
                <div><strong>Date of Hire:</strong> {{ \Carbon\Carbon::parse($employee->date_of_hire)->format('Y-m-d') }}</div>
                <div class="col-span-full"><strong>Address:</strong> {{ $employee->address }}</div>
            </div>
        </div>

        {{-- Month Filter --}}
        <form method="GET" action="{{ route('salaries.show', $employee->id) }}" class="bg-white rounded-xl shadow p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Select Month</h2>
            <div class="flex flex-col sm:flex-row sm:items-end gap-4">
                <div class="w-full sm:w-1/3">
                    <label for="month" class="block text-sm font-medium text-gray-700">Month</label>
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
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Assigned Shift Locations</h2>
            @if ($siteSummaries)
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left text-gray-700">
                        <thead class="bg-gray-100 font-semibold text-gray-800 border-b">
                            <tr>
                                <th class="px-4 py-2">Location</th>
                                <th class="px-4 py-2">Shifts</th>
                                <th class="px-4 py-2">Rate</th>
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

        {{-- Salary Components --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Special OT --}}
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Special Overtime</h2>
                <div class="space-y-2 text-sm text-gray-700">
                    <div><strong>Special OT Hours:</strong> {{ $specialOtHours }}</div>
                    <div><strong>Special OT Rate:</strong> Rs. 200.00</div>
                    <div><strong>Special OT Earnings:</strong> Rs. {{ number_format($specialOtHours * 200, 2) }}</div>
                </div>
            </div>

            {{-- Basic Salary and Allowances --}}
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Basic Salary & Allowances</h2>
                <div class="space-y-2 text-sm text-gray-700">
                    <div><strong>Basic Salary:</strong> Rs. {{ number_format($employee->basic, 2) }}</div>
                    <div><strong>BR Allowance:</strong> Rs. {{ number_format($employee->br_allow, 2) }}</div>
                    <div><strong>Attendance Bonus:</strong> Rs. {{ number_format($employee->attendance_bonus, 2) }}</div>
                    <div><strong>Other Allowances:</strong> Rs. {{ number_format($otherAllowances, 2) }}</div>
                </div>
            </div>
        </div>

        {{-- Earnings and Deductions --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Earnings --}}
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Earnings Summary</h2>
                <div class="space-y-2 text-sm text-gray-700">
                    <div><strong>Total Shift Earnings:</strong> Rs. {{ number_format($totalShiftEarning, 2) }}</div>
                    <div><strong>OT Hours:</strong> {{ $totalOTHours }} hrs</div>
                    <div><strong>OT Rate:</strong> Rs. {{ number_format($otRate, 2) }} / hr</div>
                    <div><strong>OT Earnings:</strong> Rs. {{ number_format($otEarnings, 2) }}</div>
                    <div><strong>Sub Total:</strong> Rs. {{ number_format($subTotal, 2) }}</div>
                    <div><strong>Gross Pay:</strong> Rs. {{ number_format($grossPay, 2) }}</div>
                </div>
            </div>

            {{-- Deductions --}}
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Deductions Summary</h2>
                <div class="space-y-2 text-sm text-gray-700">
                    <div><strong>EPF (Employee 8%):</strong> Rs. {{ number_format($epfEmployee, 2) }}</div>
                    <div><strong>Salary Advances:</strong> Rs. {{ number_format($employee->totalSalaryAdvance, 2) }}</div>
                    <div><strong>Total Deductions:</strong> Rs. {{ number_format($totalDeductions, 2) }}</div>
                </div>
            </div>
        </div>

        {{-- Net Pay --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-xl font-semibold text-green-700 mb-4">Final Net Pay</h2>
            <div class="text-xl text-green-700 font-bold">Rs. {{ number_format($totalEarnings, 2) }}</div>
            <div class="mt-2 text-blue-700 text-sm">
                <strong>Employer EPF/ETF (15%):</strong> Rs. {{ number_format($epfEtfEmployer, 2) }}
            </div>
        </div>
    </div>
</x-app-layout>
