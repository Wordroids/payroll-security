<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Attendance Report - {{ $month }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 1px;
            text-align: center;
            font-size: 12px;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .employee-name {
            width: 80px;
            font-size: 8px;
        }

        .day-cell {
            width: 20px;
        }

        .text-blue {
            color: #0000ff;
        }

        .text-purple {
            color: #800080;
        }

        .text-gray {
            color: #cccccc;
        }
    </style>
</head>

<body>
    <h2 style="text-align: center;">Monthly Attendance Sheet -
        {{ Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}</h2>

    @foreach ($sites as $site)
        @if (count($site->employees) > 0)
            <div style="margin-bottom: 20px; page-break-inside: avoid;">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 150px; text-align: left;">Site: {{ $site->name }}</th>
                            @for ($d = 1; $d <= $daysInMonth; $d++)
                                <th style="width: 30px;">{{ $d }}</th>
                            @endfor
                            <th>Norm Hrs</th>
                            <th>Tot. Norm. Hrs</th>
                            <th>OT Hrs</th>
                            <th>Tot. OT Hrs</th>
                            <th>Tot. Hrs</th>
                            <th>Days</th>
                            <th>Shifts</th>
                            <th>S.P. OT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($site->employees as $employee)
                            @php
                                $dayTotal = 0;
                                $nightTotal = 0;
                                $totalNormalDayHours = 0;
                                $totalNormalNightHours = 0;
                                $totalOTDayHours = 0;
                                $totalOTNightHours = 0;
                                $specialOtHours = 0;
                            @endphp
                            <tr>
                                <td style="text-align: left;">{{ $employee->name }}</td>
                                @for ($d = 1; $d <= $daysInMonth; $d++)
                                    @php
                                        $dayHours = $attendances[$employee->id][$site->id][$d]['day'] ?? null;
                                        $nightHours = $attendances[$employee->id][$site->id][$d]['night'] ?? null;
                                        $dayTotal += is_numeric($dayHours) ? $dayHours : 0;
                                        $nightTotal += is_numeric($nightHours) ? $nightHours : 0;
                                        $normalDayHours =
                                            $attendances[$employee->id][$site->id][$d]['normal_day_hours'] ?? 0;
                                        $normalNightHours =
                                            $attendances[$employee->id][$site->id][$d]['normal_night_hours'] ?? 0;
                                        $otDayHours = $attendances[$employee->id][$site->id][$d]['ot_day_hours'] ?? 0;
                                        $otNightHours =
                                            $attendances[$employee->id][$site->id][$d]['ot_night_hours'] ?? 0;
                                        $totalNormalDayHours += $normalDayHours;
                                        $totalNormalNightHours += $normalNightHours;
                                        $totalOTDayHours += $otDayHours;
                                        $totalOTNightHours += $otNightHours;
                                        if ($site->has_special_ot_hours) {
                                            $specialOtHours = $specialOtHours + max($dayHours - 12, 0);
                                        }
                                    @endphp
                                    <td>
                                        @if ($dayHours || $nightHours)
                                            <div style="line-height: 1.2;">
                                                @if ($dayHours)
                                                    <span class="text-blue">{{ $dayHours }}</span><br>
                                                @endif
                                                @if ($nightHours)
                                                    <span class="text-purple">{{ $nightHours }}</span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-gray">-</span>
                                        @endif
                                    </td>
                                @endfor
                                <td>
                                    <div style="line-height: 1.2;">
                                        <span class="text-blue">{{ $totalNormalDayHours }}</span><br>
                                        <span class="text-purple">{{ $totalNormalNightHours }}</span>
                                    </div>
                                </td>
                                <td>{{ $totalNormalDayHours + $totalNormalNightHours }}</td>
                                <td>
                                    <div style="line-height: 1.2;">
                                        <span class="text-blue">{{ $totalOTDayHours }}</span><br>
                                        <span class="text-purple">{{ $totalOTNightHours }}</span>
                                    </div>
                                </td>
                                <td>{{ $totalOTDayHours + $totalOTNightHours }}</td>
                                <td>{{ $totalNormalDayHours + $totalNormalNightHours + $totalOTDayHours + $totalOTNightHours }}
                                </td>
                                <td>{{ number_format(($totalNormalDayHours + $totalNormalNightHours + $totalOTDayHours + $totalOTNightHours) / 24, 2) }}
                                </td>
                                <td>{{ number_format(($totalNormalDayHours + $totalNormalNightHours + $totalOTDayHours + $totalOTNightHours) / 12, 2) }}
                                </td>
                                <td>{{ $specialOtHours }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endforeach
</body>

</html>
