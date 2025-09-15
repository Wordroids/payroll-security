<!DOCTYPE html>
<html>
<head>
    <title>Salary Advances Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #111827;
            margin-bottom: 5px;
            font-size: 18px;
        }
        .subtitle {
            text-align: center;
            color: #374151;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .info {
            margin-bottom: 15px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 12px;
        }
        th {
            background-color: #f3f4f6;
            text-align: left;
            padding: 8px;
            border: 1px solid #e5e7eb;
            font-weight: 600;
            color: #111827;
        }
        td {
            padding: 8px;
            border: 1px solid #e5e7eb;
            color: #4b5563;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .total-row {
            font-weight: 600;
            background-color: #f3f4f6;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 11px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <h1>Salary Advances Report</h1>

    <div class="subtitle">
        @if($showAll)
            All Records
        @elseif($month)
            {{ \Carbon\Carbon::parse($month)->format('F Y') }}
        @elseif($date)
            {{ \Carbon\Carbon::parse($date)->format('d F Y') }}
        @else
            {{ \Carbon\Carbon::now()->format('d F Y') }}
        @endif
    </div>

    <div class="info">
        Generated on: {{ \Carbon\Carbon::now()->format('Y-m-d') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 10%">Emp No</th>
                <th style="width: 20%">Name</th>
                <th style="width: 15%">Amount</th>
                <th style="width: 15%">Date</th>
                <th style="width: 40%">Reason</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($employees as $employee)
                @foreach ($employee->salaryAdvances as $advance)
                    <tr>
                        <td>{{ $employee->emp_no }}</td>
                        <td>{{ $employee->name }}</td>
                        <td>Rs. {{ number_format($advance->amount, 2) }}</td>
                        <td>{{ \Carbon\Carbon::parse($advance->advance_date)->format('Y-m-d') }}</td>
                        <td>{{ $advance->reason }}</td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">No salary advances found</td>
                </tr>
            @endforelse

            @if($employees->count() > 0)
                <tr class="total-row">
                    <td colspan="2">Total</td>
                    <td>Rs. {{ number_format($totalSalaryAdvances, 2) }}</td>
                    <td colspan="2"></td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        {{ config('app.name') }} - Salary Advances Report
    </div>
</body>
</html>
