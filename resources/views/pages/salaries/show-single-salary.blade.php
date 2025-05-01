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
            <div><strong>Date of Birth:</strong> {{ \Carbon\Carbon::parse($employee->date_of_birth)->format('Y-m-d') }}
            </div>
            <div><strong>Date of Hire:</strong> {{ \Carbon\Carbon::parse($employee->date_of_hire)->format('Y-m-d') }}
            </div>
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

            @if ($employee->sites->count())

                <div class="grid grid-cols-1">
                    <table>
                        <thead>
                            <th class="text-left text-md">Location</th>
                            <th class="text-left text-md">No. of Shifts</th>
                            <th class="text-left text-md">Shift Rate</th>
                        </thead>
                        @php
                            $totalShiftEarning = 0;
                            $totalOTHours = 0;
                        @endphp

                        <tbody>
                            @foreach ($employee->sites as $site)
                                @php
                                    $dayTotal = 0;
                                    $nightTotal = 0;
                                    $totalNormalDayHours = 0;
                                    $totalNormalNightHours = 0;
                                    $totalOTDayHours = 0;
                                    $totalOTNightHours = 0;
                                @endphp

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
                                    @endphp
                                @endfor

                                @php
                                    $totalShiftEarning +=
                                        (($totalNormalDayHours +
                                            $totalNormalNightHours +
                                            $totalOTDayHours +
                                            $totalOTNightHours) /
                                            12) *
                                        $site->guard_shift_rate;
                                    $totalOTHours += $totalOTDayHours + $totalOTNightHours;
                                @endphp

                                <tr>
                                    <td class="text-sm w-96">{{ $site->name }} - {{ $site->location }}</td>
                                    <td class="text-sm">
                                        {{ ($totalNormalDayHours + $totalNormalNightHours + $totalOTDayHours + $totalOTNightHours) / 12 }}
                                    </td>
                                    <td class="text-sm">Rs .{{ $site->guard_shift_rate }}</td>
                                </tr>
                            @endforeach

                        </tbody>

                    </table>

                    <div class="mt-4">
                        <h3 class="text-lg font-semibold text-gray-800">Total Shift Earnings: Rs.
                            {{ $totalShiftEarning }}.00</h3>
                    </div>

                    <div class="mt-4">
                        <h3 class="text-lg font-semibold text-gray-800">Total OT Hours: {{ $totalOTHours }} Hours</h3>
                    </div>

                    <div class="mt-4">
                        <h3 class="text-lg font-semibold text-gray-800">OT Rate:
                            {{ number_format(((($employee->basic + $employee->br_allow) / 8) * 1.5) / 26, 2) }} Hours</h3>
                    </div>

                    <div class="mt-4">
                        <h3 class="text-lg font-semibold text-gray-800">Basic Salary: Rs.{{ $employee->basic }}.00</h3>
                    </div>

                    <div class="mt-4">
                        <h3 class="text-lg font-semibold text-gray-800">BR Allowance: Rs.{{ $employee->br_allow }}.00
                        </h3>
                    </div>

                    <div class="mt-4">
                        <h3 class="text-lg font-semibold text-gray-800">OT Earnings:
                            Rs.{{ number_format((((($employee->basic + $employee->br_allow) / 8) * 1.5) / 26) * $totalOTHours, 2) }}
                        </h3>
                    </div>


                    <div class="mt-4">
                        <h3 class="text-lg font-semibold text-gray-800">Attendance Bonus:
                            Rs.{{ $employee->attendance_bonus }}.00
                        </h3>
                    </div>

                    <div class="mt-4">
                        <h3 class="text-lg font-semibold text-gray-800">Other Allowances: Rs.{{ number_format($totalShiftEarning - ($employee->attendance_bonus + $employee->br_allow +$employee->basic + ((((($employee->basic + $employee->br_allow) / 8) * 1.5) / 26) * $totalOTHours))) }}.00
                        </h3>
                    </div>

                    <div class="mt-4">
                        <h3 class="text-lg font-semibold text-gray-800">Sub total: Rs.{{ number_format(($employee->attendance_bonus + $employee->br_allow +$employee->basic + ((((($employee->basic + $employee->br_allow) / 8) * 1.5) / 26) * $totalOTHours))) }}.00
                        </h3>
                    </div>

                    <div class="mt-4">
                        <h3 class="text-lg font-semibold text-gray-800">Gross pay: Rs.{{ $grossPay = $totalShiftEarning }}.00
                        </h3>
                    </div>

                    
                    <div class="mt-4">
                        <h3 class="text-lg font-semibold text-gray-800">EPF 8%: Rs.{{ $tottalepf = (($employee->basic + $employee->br_allow) / 100) * 8 }}.00
                        </h3>
                    </div>

                    <div class="mt-4">
                        <h3 class="text-lg font-semibold text-gray-800">Salary Advacnces: Rs.{{ $employee->totalSalaryAdvance }}.00
                        </h3>
                    </div>

                    <div class="mt-4">
                        <h3 class="text-lg font-semibold text-gray-800">Total Deductions: Rs.{{ $totalDeductions = $tottalepf + $employee->totalSalaryAdvance }}.00
                        </h3>
                    </div>

                    <div class="mt-4">
                        <h3 class="text-lg font-semibold text-gray-800">Total Earnings: Rs.{{ $grossPay - $totalDeductions }}.00
                        </h3>
                    </div>

                    <div class="mt-4">
                        <h3 class="text-lg font-semibold text-gray-800">EPF/ETF 15%: Rs.{{ number_format( ($employee->basic /100) * 15 ) }}.00
                        </h3>
                    </div>






                </div>
            @else
                <p class="text-gray-500 text-sm">No shift locations assigned.</p>
            @endif
        </div>






    </div>
</x-app-layout>
