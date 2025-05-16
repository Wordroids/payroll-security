@php
    use Carbon\Carbon;
    $daysInMonth = Carbon::createFromFormat('Y-m', $month)->daysInMonth;
@endphp

<x-app-layout>
    <div class="">
        <h2 class="text-2xl font-semibold mb-4">Monthly Attendance Sheet</h2>

        {{-- Filter Form --}}
        <form method="GET" action="{{ route('attendances.index') }}" class="flex gap-4 items-end mb-6">
            <div>
                <label for="month" class="text-sm text-gray-700">Month</label>
                <input type="month" name="month" id="month" value="{{ $month }}"
                    class="border rounded p-2 w-full">
            </div>

            <div>
                <label for="employee_id" class="text-sm text-gray-700">Employee</label>
                <select name="employee_id" id="employee_id" class="border rounded p-2 w-full">
                    <option value="">-- All Employees --</option>
                    @foreach ($allEmployees as $emp)
                        <option value="{{ $emp->id }}" {{ $employeeId == $emp->id ? 'selected' : '' }}>
                            {{ $emp->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="pt-2">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Filter</button>
            </div>
        </form>

        {{-- <div class="overflow-x-auto bg-white rounded shadow">
            <table class="min-w-max w-full text-sm border-collapse border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-2 py-1 text-left">Employee Name</th>
                        @for ($d = 1; $d <= $daysInMonth; $d++)
                            <th class="border px-2 py-1 text-center">{{ $d }}</th>
                        @endfor
                        <th class="border px-2 py-1 text-center">Norm Hrs</th>
                        <th class="border px-2 py-1 text-center">Tot. Norm. Hrs</th>
                        <th class="border px-2 py-1 text-center">OT Hrs</th>
                        <th class="border px-2 py-1 text-center">Tot. OT Hrs</th>
                        <th class="border px-2 py-1 text-center">Tot. Hrs</th>
                        <th class="border px-2 py-1 text-center">Days</th>
                        <th class="border px-2 py-1 text-center">Shifts</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sites as $site)
                        <tr>
                            <td colspan="{{ $daysInMonth + 8 }}" class="bg-gray-200 font-semibold px-4 py-2 text-indigo-700">
                                Site: {{ $site->name }}
                            </td>
                        </tr>

                        @foreach ($site->employees as $employee)
                            @php
                                $dayTotal = 0;
                                $nightTotal = 0;
                                $totalNormalDayHours = 0;
                                $totalNormalNightHours = 0;
                                $totalOTDayHours = 0;
                                $totalOTNightHours = 0;
                            @endphp
                            <tr>
                                <td class="border px-2 py-1">{{ $employee->name }}</td>

                                @for ($d = 1; $d <= $daysInMonth; $d++)
                                    @php
                                        $dayHours = $attendances[$employee->id][$d]['day'] ?? null;
                                        $nightHours = $attendances[$employee->id][$d]['night'] ?? null;
                                        $dayTotal += is_numeric($dayHours) ? $dayHours : 0;
                                        $nightTotal += is_numeric($nightHours) ? $nightHours : 0;
                                        $normalDayHours = $attendances[$employee->id][$d]['normal_day_hours'] ?? 0;
                                        $normalNightHours = $attendances[$employee->id][$d]['normal_night_hours'] ?? 0;
                                        $otDayHours = $attendances[$employee->id][$d]['ot_day_hours'] ?? 0;
                                        $otNightHours = $attendances[$employee->id][$d]['ot_night_hours'] ?? 0;
                                        $totalNormalDayHours += $normalDayHours;
                                        $totalNormalNightHours += $normalNightHours;
                                        $totalOTDayHours += $otDayHours;
                                        $totalOTNightHours += $otNightHours;
                                    @endphp

                                    <td class="border px-1 py-1 text-center">
                                        @if ($dayHours || $nightHours)
                                            <div class="text-xs leading-tight">
                                                @if ($dayHours)
                                                    <span class="text-blue-700">D: {{ $dayHours }}</span><br>
                                                @endif
                                                @if ($nightHours)
                                                    <span class="text-purple-700">N: {{ $nightHours }}</span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                @endfor

                                <td class="border px-2 py-1 text-center text-blue-700 font-semibold">
                                    <div class="text-xs leading-tight">
                                        <span class="text-blue-700">D: {{ $totalNormalDayHours }}</span><br>
                                        <span class="text-purple-700">N: {{ $totalNormalNightHours }}</span>
                                    </div>
                                </td>

                                <td class="border px-2 py-1 text-center text-purple-700 font-semibold">
                                    {{ $totalNormalDayHours + $totalNormalNightHours }}
                                </td>

                                <td class="border px-2 py-1 text-center font-semibold">
                                    <div class="text-xs leading-tight">
                                        <span class="text-blue-700">D: {{ $totalOTDayHours }}</span><br>
                                        <span class="text-purple-700">N: {{ $totalOTNightHours }}</span>
                                    </div>
                                </td>

                                <td class="border px-2 py-1 text-center font-semibold text-xs leading-tight">
                                    {{ $totalOTDayHours + $totalOTNightHours }}
                                </td>

                                <td class="border px-2 py-1 text-center font-semibold text-xs leading-tight">
                                    {{ $totalNormalDayHours + $totalNormalNightHours + $totalOTDayHours + $totalOTNightHours }}
                                </td>

                                <td class="border px-2 py-1 text-center font-semibold text-xs leading-tight">
                                    {{ ($totalNormalDayHours + $totalNormalNightHours + $totalOTDayHours + $totalOTNightHours) / 24 }}
                                </td>

                                <td class="border px-2 py-1 text-center font-semibold text-xs leading-tight">
                                    {{ ($totalNormalDayHours + $totalNormalNightHours + $totalOTDayHours + $totalOTNightHours) / 12 }}
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div> --}}
        @foreach ($sites as $site)
            <div class="overflow-x-auto bg-white rounded shadow mb-20">

                <table class="min-w-max w-full text-sm border-collapse border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th
                                class="border text-left bg-gray-200 font-semibold px-4 py-2 min-w-48 max-w-48 text-indigo-700">
                                Site:
                                {{ $site->name }}
                            </th>

                            @for ($d = 1; $d <= $daysInMonth; $d++)
                                <th class="border px-2 py-1 text-center">{{ $d }}</th>
                            @endfor
                            <th class="border px-2 py-1 text-center">Norm Hrs</th>
                            <th class="border px-2 py-1 text-center">Tot. Norm. Hrs</th>
                            <th class="border px-2 py-1 text-center">OT Hrs</th>
                            <th class="border px-2 py-1 text-center">Tot. OT Hrs</th>
                            <th class="border px-2 py-1 text-center">Tot. Hrs</th>
                            <th class="border px-2 py-1 text-center">Days</th>
                            <th class="border px-2 py-1 text-center">Shifts</th>
                            <th class="border px-2 py-1 text-center">S.P. OT</th>
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
                                <td class="border px-2 py-1">{{ $employee->name }}</td>

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
                                           $specialOtHours = $specialOtHours + max( $dayHours - 12, 0);
                                        }


                                    @endphp

                                    <td class="border px-1 py-1 text-center">
                                        @if ($dayHours || $nightHours)
                                            <div class="text-xs leading-tight">
                                                @if ($dayHours)
                                                    <span class="text-blue-700"> {{ $dayHours }}</span><br>
                                                @endif
                                                @if ($nightHours)
                                                    <span class="text-purple-700">{{ $nightHours }}</span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                @endfor



                                <td class="border px-2 py-1 text-center text-blue-700 font-semibold">
                                    <div class="text-xs leading-tight">
                                        <span class="text-blue-700"> {{ $totalNormalDayHours }}</span><br>
                                        <span class="text-purple-700"> {{ $totalNormalNightHours }}</span>
                                    </div>
                                </td>

                                <td class="border px-2 py-1 text-center text-purple-700 font-semibold">
                                    {{ $totalNormalDayHours + $totalNormalNightHours }}
                                </td>

                                <td class="border px-2 py-1 text-center font-semibold">
                                    <div class="text-xs leading-tight">
                                        <span class="text-blue-700"> {{ $totalOTDayHours }}</span><br>
                                        <span class="text-purple-700"> {{ $totalOTNightHours }}</span>
                                    </div>
                                </td>

                                <td class="border px-2 py-1 text-center font-semibold text-xs leading-tight">
                                    {{ $totalOTDayHours + $totalOTNightHours }}
                                </td>

                                <td class="border px-2 py-1 text-center font-semibold text-xs leading-tight">
                                    {{ $totalNormalDayHours + $totalNormalNightHours + $totalOTDayHours + $totalOTNightHours }}
                                </td>

                                <td class="border px-2 py-1 text-center font-semibold text-xs leading-tight">
                                    {{ ($totalNormalDayHours + $totalNormalNightHours + $totalOTDayHours + $totalOTNightHours) / 24 }}
                                </td>

                                <td class="border px-2 py-1 text-center font-semibold text-xs leading-tight">
                                    {{ ($totalNormalDayHours + $totalNormalNightHours + $totalOTDayHours + $totalOTNightHours) / 12 }}
                                </td>

                                <td class="border px-2 py-1 text-center font-semibold text-xs leading-tight">
                                    {{ $specialOtHours }}
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>

            </div>
        @endforeach
    </div>
</x-app-layout>
