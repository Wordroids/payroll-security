@php
    use Carbon\Carbon;
    $daysInMonth = Carbon::createFromFormat('Y-m', $month)->daysInMonth;
@endphp

<x-app-layout>
    <div class="p-6 max-w-3xl mx-auto bg-white shadow rounded">
        <h2 class="text-2xl font-semibold mb-4 text-gray-800">Employee Details</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
            <div><strong>Emp No:</strong> {{ $employee->emp_no }}</div>
            <div><strong>Name:</strong> {{ $employee->name }}</div>
            <div><strong>Phone:</strong> {{ $employee->phone }}</div>
            <div><strong>NIC:</strong> {{ $employee->nic }}</div>
            <div><strong>Rank:</strong> {{ $employee->rank }}</div>
            <div><strong>Date of Birth:</strong> {{ \Carbon\Carbon::parse($employee->date_of_birth)->format('Y-m-d') }}</div>
            <div><strong>Date of Hire:</strong> {{ \Carbon\Carbon::parse($employee->date_of_hire)->format('Y-m-d') }}</div>
            <div><strong>Address:</strong> {{ $employee->address }}</div>
        </div>

        <form method="GET" action="{{ route('salaries.show', $employee->id) }}" class="my-6 flex items-end gap-4">
            <div>
                <label for="month" class="block text-sm font-medium text-gray-700">Select Month</label>
                <input type="month" name="month" id="month" value="{{ $month }}"
                    class="border rounded p-2 text-sm w-full" required>
            </div>
            <div class="pt-2">
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700">
                    Filter
                </button>
            </div>
        </form>

        <div class="mt-12">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Assigned Shift Locations</h2>

            @if ($siteSummaries)
                <table class="w-full border text-sm">
                    <thead class="bg-gray-100 text-left">
                        <tr>
                            <th class="p-2">Location</th>
                            <th class="p-2">No. of Shifts</th>
                            <th class="p-2">Shift Rate</th>
                            <th class="p-2">Earnings</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($siteSummaries as $summary)
                            <tr>
                                <td class="p-2">{{ $summary['site']->name }} - {{ $summary['site']->location }}</td>
                                <td class="p-2">{{ number_format($summary['shifts'], 2) }}</td>
                                <td class="p-2">Rs. {{ $summary['site']->guard_shift_rate }}</td>
                                <td class="p-2">Rs. {{ number_format($summary['earning'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-6 space-y-2 text-sm text-gray-800">
                    <p><strong>Total Shift Earnings:</strong> Rs. {{ number_format($totalShiftEarning, 2) }}</p>
                    <p><strong>Total OT Hours:</strong> {{ $totalOTHours }} Hours</p>
                    <p><strong>OT Rate:</strong> Rs. {{ number_format($otRate, 2) }} per Hour</p>
                    <p><strong>OT Earnings:</strong> Rs. {{ number_format($otEarnings, 2) }}</p>
                    <p><strong>Attendance Bonus:</strong> Rs. {{ number_format($employee->attendance_bonus, 2) }}</p>
                    <p><strong>Basic Salary:</strong> Rs. {{ number_format($employee->basic, 2) }}</p>
                    <p><strong>BR Allowance:</strong> Rs. {{ number_format($employee->br_allow, 2) }}</p>
                    <p><strong>Other Allowances:</strong> Rs. {{ number_format($otherAllowances, 2) }}</p>
                    <p><strong>Sub Total:</strong> Rs. {{ number_format($subTotal, 2) }}</p>
                    <p><strong>Gross Pay:</strong> Rs. {{ number_format($grossPay, 2) }}</p>
                    <p><strong>EPF (Employee 8%):</strong> Rs. {{ number_format($epfEmployee, 2) }}</p>
                    <p><strong>Salary Advances:</strong> Rs. {{ number_format($employee->totalSalaryAdvance, 2) }}</p>
                    <p><strong>Total Deductions:</strong> Rs. {{ number_format($totalDeductions, 2) }}</p>
                    <p><strong>Total Earnings (Net Pay):</strong> Rs. {{ number_format($totalEarnings, 2) }}</p>
                    <p><strong>EPF/ETF (Employer 15%):</strong> Rs. {{ number_format($epfEtfEmployer, 2) }}</p>
                </div>
            @else
                <p class="text-gray-500 text-sm">No shift locations assigned.</p>
            @endif
        </div>
    </div>
</x-app-layout>
