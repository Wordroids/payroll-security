<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Salary Overview - {{ Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            margin: 0;
            padding: 0;
        }

        .report-header {
            text-align: center;
            margin-bottom: 10px;
        }

        .report-title {
            font-size: 14px;
            font-weight: bold;
        }

        .report-subtitle {
            font-size: 11px;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 3px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .employee-name {
            text-align: left;
            width: 120px;
        }

        .text-right {
            text-align: right;
        }

        .text-bold {
            font-weight: bold;
        }

        .text-green {
            color: #008000;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <div class="report-header">
        <div class="report-title">Salary Overview Report</div>
        <div class="report-subtitle">{{ Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}</div>
        @if ($selectedEmployee)
            <div class="report-subtitle">Employee: {{ \App\Models\Employee::find($selectedEmployee)->name }}</div>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th class="employee-name">Emp No</th>
                <th class="employee-name">Name</th>
                <th class="epf-etf-status">EPF/ETF</th>
                <th>Shifts</th>
                <th>Basic</th>
                <th>OT Earnings</th>
                <th>Special OT</th>
                <th>Shift Earnings</th>
                <th>Att. Bonus</th>
                <th>Other Allow</th>
                <th>Sub Total</th>
                <th>Gross Pay</th>
                <th>Salary Adv</th>
                <th>Meals</th>
                <th>Uniform</th>
                <th>EPF</th>
                <th>Total Deduct</th>
                <th>Net Pay</th>
                <th>EPF 12%</th>
                <th>ETF 3%</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($salaryData as $data)
                <tr>
                    <td class="employee-name">{{ $data['employee']->emp_no }}</td>
                    <td class="employee-name">{{ $data['employee']->name }}</td>
                    <td class="epf-etf-status">{{ $data['employee']->include_epf_etf ? 'Yes' : 'No' }}</td>
                    <td>{{ number_format($data['total_shifts'], 2) }}</td>
                    <td class="text-right">{{ number_format($data['basic'], 2) }}</td>
                    <td class="text-right">{{ number_format($data['ot_earnings'], 2) }}</td>
                    <td class="text-right">{{ number_format($data['special_ot_earnings'], 2) }}</td>
                    <td class="text-right">{{ number_format($data['totalShiftEarning'], 2) }}</td>
                    <td class="text-right">{{ number_format($data['attendance_bonus'], 2) }}</td>
                    <td class="text-right">{{ number_format($data['other_allowances'], 2) }}</td>
                    <td class="text-right text-bold">{{ number_format($data['sub_total'], 2) }}</td>
                    <td class="text-right text-bold">{{ number_format($data['gross_pay'], 2) }}</td>
                    <td class="text-right">{{ number_format($data['salary_advance'], 2) }}</td>
                    <td class="text-right">{{ number_format($data['meal_deductions'], 2) }}</td>
                    <td class="text-right">{{ number_format($data['uniform_deductions'], 2) }}</td>
                    <td class="text-right">{{ $data['employee']->include_epf_etf ? number_format($data['epf_deduct_employee'], 2) : 'Excluded' }}</td>
                    <td class="text-right">{{ number_format($data['total_deductions'], 2) }}</td>
                    <td class="text-right text-bold text-green">{{ number_format($data['net_pay'], 2) }}</td>
                      <td class="text-right">{{ $data['employee']->include_epf_etf ? number_format($data['epf_employee'], 2) : 'Excluded' }}</td>
                    <td class="text-right">{{ $data['employee']->include_epf_etf ? number_format($data['etf_employee'], 2) : 'Excluded' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
