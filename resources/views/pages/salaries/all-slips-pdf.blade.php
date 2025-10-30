<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>All Salary Slips - {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.2;
            font-size: 9px;
            margin: 0;
            padding: 5mm;
        }

        .page {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            page-break-after: always;
        }

        .slip-container {
            width: 49%;
            box-sizing: border-box;
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
            border-collapse: separate;
            border-spacing: 8px;
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

        .text-red-700 {
            color: #b91c1c;
        }

        .total-row {
            font-weight: bold;
            border-top: 1px solid #000;
        }

        .font-semibold {
            font-weight: 600;
        }

        .special-ot-table th,
        .special-ot-table td {
            font-size: 7px;
            padding: 0.5mm;
        }

        .receipt-title {
            font-size: 8px;
            font-weight: bold;
            text-align: center;
            margin-top: 2mm;
            margin-bottom: 1mm;
        }

        .outer-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 5px;
            page-break-after: always;
        }

        .outer-table:last-child {
            page-break-after: auto;
        }

        @media print {
            body {
                margin: 0;
            }

            .page {
                page-break-after: always;
            }
        }
    </style>
</head>

<body>
    @foreach (array_chunk($slipsData, 2) as $pageIndex => $slipPair)
        <table class="outer-table">
            <tr>
                @foreach ($slipPair as $slipData)
                    <td style="width:50%; vertical-align: top; padding:10px; border:0;">
                        <div class="form-container">
                            <!-- Header -->
                            <div class="header">
                                <div class="company-name">SMART SYNDICATES</div>
                                <div class="slip-title">Security & Investigations</div>
                                <div class="slip-title">
                                    Salary Slip - {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}
                                </div>
                            </div>

                            <!-- Employee Information -->
                            <table class="info-table">
                                <tr>
                                    <td class="font-semibold" style="width: 30%">Name</td>
                                    <td style="width: 70%">{{ $slipData['employee']->name }}</td>
                                </tr>
                                <tr>
                                    <td class="font-semibold">E.P.F No</td>
                                    <td>{{ $slipData['employee']->emp_no }}</td>
                                </tr>
                                <tr>
                                    <td class="font-semibold">Month</td>
                                    <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="font-semibold">Rank</td>
                                    <td>{{ $slipData['employee']->rank }}</td>
                                </tr>
                                <tr>
                                    <td class="font-semibold">EPF/ETF</td>
                                    <td>{{ $slipData['employee']->include_epf_etf ? 'Included' : 'Excluded' }}</td>
                                </tr>
                            </table>

                            <!-- Earnings -->
                            <table class="section-table">
                                <thead>
                                    <tr>
                                        <th style="width:65%">Earnings</th>
                                        <th class="text-right" style="width:35%">Amount (Rs.)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Basic Salary</td>
                                        <td class="text-right">{{ number_format($slipData['employee']->basic, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Shift Earnings</td>
                                        <td class="text-right">{{ number_format($slipData['totalShiftEarning'], 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Over Time</td>
                                        <td class="text-right">{{ number_format($slipData['otEarnings'], 2) }}</td>
                                    </tr>
                                    @if ($slipData['performanceAllowance'] > 0)
                                        <tr>
                                            <td>Performance Allowance</td>
                                            <td class="text-right">
                                                {{ number_format($slipData['performanceAllowance'], 2) }}</td>
                                        </tr>
                                    @endif
                                    @if ($slipData['specialOtEarnings'] > 0)
                                        <tr>
                                            <td>Special Overtime</td>
                                            <td class="text-right">
                                                {{ number_format($slipData['specialOtEarnings'], 2) }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td>Attendance Allowance</td>
                                        <td class="text-right">
                                            {{ number_format($slipData['employee']->attendance_bonus, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Other Allowance</td>
                                        <td class="text-right">{{ number_format($slipData['otherAllowances'], 2) }}
                                        </td>
                                    </tr>
                                    <tr class="total-row">
                                        <td>Gross Pay</td>
                                        <td class="text-right">{{ number_format($slipData['grossPay'], 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- Deductions -->
                            <table class="section-table">
                                <thead>
                                    <tr>
                                        <th style="width:65%">Deductions</th>
                                        <th class="text-right" style="width:35%">Amount (Rs.)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($slipData['employee']->include_epf_etf)
                                        <tr>
                                            <td class="text-red-700">E.P.F</td>
                                            <td class="text-right text-red-700">
                                                {{ number_format($slipData['epfDeductEmployee'], 2) }}</td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td>EPF/ETF</td>
                                            <td class="text-right">Excluded</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td class="text-red-700">Salary Advance</td>
                                        <td class="text-right text-red-700">
                                            {{ number_format($slipData['totalSalaryAdvance'], 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-red-700">Meals</td>
                                        <td class="text-right text-red-700">
                                            {{ number_format($slipData['mealDeductions'], 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-red-700">Uniforms</td>
                                        <td class="text-right text-red-700">
                                            {{ number_format($slipData['uniformDeductions'], 2) }}</td>
                                    </tr>
                                    <tr class="total-row">
                                        <td class="text-red-700">Total Deduction</td>
                                        <td class="text-right text-red-700">
                                            {{ number_format($slipData['totalDeductions'], 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- Summary -->
                            <table class="info-table">
                                <tr class="font-semibold">
                                    <td style="width:65%">Total Earning</td>
                                    <td class="text-right" style="width:35%">
                                        {{ number_format($slipData['totalEarnings'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Total Shifts Done</td>
                                    <td class="text-right">
                                        {{ number_format(array_sum(array_column($slipData['siteSummaries'], 'shifts')), 0) }}
                                    </td>
                                </tr>
                            </table>

                            <!-- Receipt -->
                            <div class="receipt-section">
                                <div class="receipt-title">RECEIPT</div>
                                <table class="info-table">
                                    <tr>
                                        <td class="font-semibold" style="width:30%">Name</td>
                                        <td style="width:70%">{{ $slipData['employee']->name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-semibold">E.P.F No</td>
                                        <td>{{ $slipData['employee']->emp_no }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-semibold">EPF/ETF Status</td>
                                        <td>{{ $slipData['employee']->include_epf_etf ? 'Included' : 'Excluded' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-semibold">Signature</td>
                                        <td>................................</td>
                                    </tr>
                                    <tr>
                                        <td class="font-semibold">Date</td>
                                        <td>{{ \Carbon\Carbon::now()->format('Y-m-d') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </td>
                @endforeach

                @if (count($slipPair) == 1)
                    <td style="width:50%"></td>
                @endif
            </tr>
        </table>
    @endforeach
</body>

</html>
