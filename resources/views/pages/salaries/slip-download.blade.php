<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Salary Slip - {{ $employee->name }} - {{ $month }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.2;
            font-size: 9px;
            width: 7cm;
            margin: 0;
            padding: 2mm;
        }

        .form-container {
            border: 1px solid #000;
            padding: 2mm;
            width: 100%;
            box-sizing: border-box;
        }

        .header {
            text-align: center;
            margin-bottom: 2mm;
        }

        .company-name {
            font-size: 10px;
            font-weight: bold;
        }

        .slip-title {
            font-size: 8px;
            margin: 1mm 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1mm 0;
            page-break-inside: avoid;
        }

        th,
        td {
            padding: 1mm;
            text-align: left;
            border-bottom: 0.5px solid #ddd;
            font-size: 8px;
        }

        th {
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .border-b-1 {
            border-bottom-width: 1px;
        }

        .border-t-1 {
            border-top-width: 1px;
        }

        .border-gray-300 {
            border-color: #d1d5db;
        }

        .font-semibold {
            font-weight: 600;
        }

        .mb-2 {
            margin-bottom: 2mm;
        }

        .py-1 {
            padding-top: 1mm;
            padding-bottom: 1mm;
        }

        .w-1\/3 {
            width: 33%;
        }

        .w-2\/3 {
            width: 67%;
        }

        .compact-row {
            margin: 0;
            padding: 0;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <div class="header">
            <div class="company-name">SMART SYNDICATES</div>
            <div class="slip-title">Security & Investigations</div>
            <div class="slip-title">Salary Slip - {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}
            </div>
        </div>

        <table class="compact-row">
            <tr>
                <td class="font-semibold w-1/3">Name</td>
                <td>{{ $employee->name }}</td>
            </tr>
            <tr>
                <td class="font-semibold">E.P.F No</td>
                <td>{{ $employee->emp_no }}</td>
            </tr>
            <tr>
                <td class="font-semibold">Month</td>
                <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}</td>
            </tr>
            <tr>
                <td class="font-semibold">Rank</td>
                <td>{{ $employee->rank }}</td>
            </tr>
        </table>

        <table class="compact-row">
            <thead>
                <tr class="border-b-1 border-gray-300">
                    <th class="w-2/3">Earnings</th>
                    <th class="text-right">Amount (Rs.)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Basic Salary</td>
                    <td class="text-right">{{ number_format($employee->basic, 2) }}</td>
                </tr>
                <tr>
                    <td>Budgetary Allowances</td>
                    <td class="text-right">{{ number_format($employee->br_allow, 2) }}</td>
                </tr>
                <tr>
                    <td>Over Time</td>
                    <td class="text-right">{{ number_format($otEarnings, 2) }}</td>
                </tr>
                <tr>
                    <td>Attendance Allowance</td>
                    <td class="text-right">{{ number_format($employee->attendance_bonus, 2) }}</td>
                </tr>
                <tr>
                    <td>Other Allowance</td>
                    <td class="text-right">{{ number_format($otherAllowances, 2) }}</td>
                </tr>
                <tr class="border-b-1 border-gray-300 font-semibold">
                    <td>Gross Pay</td>
                    <td class="text-right">{{ number_format($grossPay, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <table class="compact-row">
            <thead>
                <tr class="border-b-1 border-gray-300">
                    <th colspan="2">Deductions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="w-2/3">E.P.F 8%</td>
                    <td class="text-right">{{ number_format($epfEmployee, 2) }}</td>
                </tr>
                <tr>
                    <td>Salary Advance</td>
                    <td class="text-right">{{ number_format($totalSalaryAdvance, 2) }}</td>
                </tr>
                <tr>
                    <td>Meals</td>
                    <td class="text-right">{{ number_format($mealDeductions, 2) }}</td>
                </tr>
                <tr>
                    <td>Uniforms</td>
                    <td class="text-right">{{ number_format($uniformDeductions, 2) }}</td>
                </tr>

                <tr class="border-b-1 border-gray-300 font-semibold">
                    <td>Total Deduction</td>
                    <td class="text-right">{{ number_format($totalDeductions, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <table class="compact-row">
            <tr class="font-semibold">
                <td class="w-2/3">Total Earning</td>
                <td class="text-right">{{ number_format($totalEarnings, 2) }}</td>
            </tr>
            <tr>
                <td>Total Shifts Done</td>
                <td class="text-right">{{ number_format(array_sum(array_column($siteSummaries, 'shifts')), 2) }}</td>
            </tr>
            <tr>
                <td>Shift Rate</td>
                <td class="text-right">{{ number_format($employee->sites->first()->guard_shift_rate ?? 0, 2) }}</td>
            </tr>
        </table>

        <table class="compact-row">
            <tr>
                <td class="w-2/3">E.P.F 12%</td>
                <td class="text-right">{{ number_format(($employee->basic / 100) * 12, 2) }}</td>
            </tr>
            <tr>
                <td>E.T.F 3%</td>
                <td class="text-right">{{ number_format(($employee->basic / 100) * 3, 2) }}</td>
            </tr>
        </table>

        <div class="border-t-1 border-gray-300 pt-1 mt-2">
            <h3 class="text-center font-bold" style="font-size: 9px; margin: 1mm 0;">RECEIPT</h3>
            <table class="compact-row">
                <tr>
                    <td class="font-semibold w-1/3">Name</td>
                    <td>{{ $employee->name }}</td>
                </tr>
                <tr>
                    <td class="font-semibold">E.P.F No</td>
                    <td>{{ $employee->emp_no }}</td>
                </tr>
                <tr>
                    <td class="font-semibold">Signature</td>
                    <td>{{ $signature ?? '................' }}</td>
                </tr>
                <tr>
                    <td class="font-semibold">Date</td>
                    <td>{{ $date ?? \Carbon\Carbon::now()->format('Y-m-d') }}</td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
