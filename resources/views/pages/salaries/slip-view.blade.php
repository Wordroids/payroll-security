<div class="bg-white p-6 rounded-lg shadow max-w-4xl mx-auto">
    <div class="header text-center mb-6">
        <div class="company-name text-2xl font-bold">SMART SYNDICATES</div>
        <div class="slip-title text-lg">Security & Investigations</div>
        <div class="slip-title text-lg">Salary Slip -
            {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}</div>
    </div>

    <table class="w-full border-collapse mb-6">
        <tr>
            <td class="py-2 font-semibold w-1/3">Name</td>
            <td class="py-2">{{ $employee->name }}</td>
        </tr>
        <tr>
            <td class="py-2 font-semibold">E.P.F No</td>
            <td class="py-2">{{ $employee->emp_no }}</td>
        </tr>
        <tr>
            <td class="py-2 font-semibold">Month</td>
            <td class="py-2">{{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}</td>
        </tr>
        <tr>
            <td class="py-2 font-semibold">Rank</td>
            <td class="py-2">{{ $employee->rank }}</td>
        </tr>
    </table>

    <table class="w-full border-collapse mb-6">
        <thead>
            <tr class="border-b-2 border-gray-300">
                <th class="py-2 text-left font-semibold w-2/3">Earnings</th>
                <th class="py-2 text-right font-semibold">Amount (Rs.)</th>
            </tr>
        </thead>
        <tbody>
            <tr class="border-b border-gray-200">
                <td class="py-2">Basic Salary</td>
                <td class="py-2 text-right">{{ number_format($employee->basic, 2) }}</td>
            </tr>
            <tr class="border-b border-gray-200">
                <td class="py-2">Shift Earnings</td>
                <td class="py-2 text-right">{{ number_format($totalShiftEarning, 2) }}</td>
            </tr>
            <tr class="border-b border-gray-200">
                <td class="py-2">Over Time</td>
                <td class="py-2 text-right">{{ number_format($otEarnings, 2) }}</td>
            </tr>
            @if($specialOtEarnings > 0)
            <tr class="border-b border-gray-200">
                <td class="py-2">Special Overtime ({{ $specialOtHours }} hrs)</td>
                <td class="py-2 text-right">{{ number_format($specialOtEarnings, 2) }}</td>
            </tr>
            @endif
            <tr class="border-b border-gray-200">
                <td class="py-2">Attendance Allowance</td>
                <td class="py-2 text-right">{{ number_format($employee->attendance_bonus, 2) }}</td>
            </tr>
            <tr class="border-b border-gray-200">
                <td class="py-2">Other Allowance</td>
                <td class="py-2 text-right">{{ number_format($otherAllowances, 2) }}</td>
            </tr>
            <tr class="border-b border-gray-200">
                <td class="py-2">Good Conduct Allowance</td>
                <td class="py-2 text-right">-</td>
            </tr>
            <tr class="border-b-2 border-gray-300 font-semibold">
                <td class="py-2">Gross Pay</td>
                <td class="py-2 text-right">{{ number_format($grossPay, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <table class="w-full border-collapse mb-6">
        <thead>
            <tr class="border-b-2 border-gray-300">
                <th colspan="2" class="py-2 text-left font-semibold">Deductions</th>
            </tr>
        </thead>
        <tbody>
             @if($employee->include_epf_etf)
            <tr class="border-b border-gray-200">
                <td class="py-2 w-2/3">E.P.F 12%</td>
                <td class="py-2 text-right">{{ number_format($epfEmployee, 2) }}</td>
            </tr>
            <tr class="border-b border-gray-200">
                <td class="py-2 w-2/3">E.T.F 3%</td>
                <td class="py-2 text-right">{{ number_format($etfEmployee, 2) }}</td>
            </tr>
            @else
            <tr class="border-b border-gray-200">
                <td class="py-2 w-2/3">EPF/ETF</td>
                <td class="py-2 text-right">Excluded</td>
            </tr>
            @endif
            <tr class="border-b border-gray-200">
                <td class="py-2">Salary Advance</td>
                <td class="py-2 text-right">{{ number_format($totalSalaryAdvance, 2) }}</td>
            </tr>
            <tr class="border-b border-gray-200">
                <td class="py-2">Meals</td>
                <td class="py-2 text-right">{{ number_format($mealDeductions, 2) }}</td>
            </tr>
            <tr class="border-b border-gray-200">
                <td class="py-2">Uniforms</td>
                <td class="py-2 text-right">{{ number_format($uniformDeductions, 2) }}</td>
            </tr>
            <tr class="border-b border-gray-200">
                <td class="py-2">Other Deduction</td>
                <td class="py-2 text-right">-</td>
            </tr>
            <tr class="border-b-2 border-gray-300 font-semibold">
                <td class="py-2">Total Deduction</td>
                <td class="py-2 text-right">{{ number_format($totalDeductions, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <table class="w-full border-collapse mb-6">
        <tr class="border-b border-gray-200 font-semibold">
            <td class="py-2 w-2/3">Total Earning</td>
            <td class="py-2 text-right">{{ number_format($totalEarnings, 2) }}</td>
        </tr>
        <tr class="border-b border-gray-200">
            <td class="py-2">Total Shifts Done</td>
            <td class="py-2 text-right">{{ number_format(array_sum(array_column($siteSummaries, 'shifts')), 2) }}</td>
        </tr>
    </table>
    @if($employee->include_epf_etf)
    <table class="w-full border-collapse mb-8">
        <tr class="border-b border-gray-200">
            <td class="py-2 w-2/3">E.P.F 12%</td>
            <td class="py-2 text-right">{{ number_format(($employee->basic / 100) * 12, 2) }}</td>
        </tr>
        <tr class="border-b border-gray-200">
            <td class="py-2">E.T.F 3%</td>
            <td class="py-2 text-right">{{ number_format(($employee->basic / 100) * 3, 2) }}</td>
        </tr>
    </table>
    @endif
    <div class="border-t-2 border-gray-300 pt-4">
        <h3 class="text-center font-bold text-lg mb-4">RECEIPT</h3>
        <table class="w-full border-collapse">
            <tr>
                <td class="py-2 font-semibold w-1/3">Name</td>
                <td class="py-2">{{ $employee->name }}</td>
            </tr>
            <tr>
                <td class="py-2 font-semibold">E.P.F No</td>
                <td class="py-2">{{ $employee->emp_no }}</td>
            </tr>
             <tr>
                <td class="py-2 font-semibold">EPF/ETF Status</td>
                <td class="py-2">{{ $employee->include_epf_etf ? 'Included' : 'Excluded' }}</td>
            </tr>
            <tr>
                <td class="py-2 font-semibold">Signature</td>
                <td class="py-2">
                    <input type="text" id="signature" class="w-full border-b border-gray-400 focus:outline-none py-1"
                        placeholder="Enter signature">
                </td>
            </tr>
            <tr>
                <td class="py-2 font-semibold">Date</td>
                <td class="py-2">
                    <input type="date" id="receiptDate"
                        class="w-full border-b border-gray-400 focus:outline-none py-1" value="{{ date('Y-m-d') }}">
                </td>
            </tr>
        </table>
    </div>
</div>
