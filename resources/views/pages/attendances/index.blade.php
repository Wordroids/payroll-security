      {{-- Attendance Table --}}
      @php
      use Carbon\Carbon;
      $daysInMonth = Carbon::createFromFormat('Y-m', $month)->daysInMonth;
  @endphp
<x-app-layout>

    <div class="p-6">
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
                    @foreach ($employees as $emp)
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

      

        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="min-w-max w-full text-sm border-collapse border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-2 py-1 text-left">#</th>
                        <th class="border px-2 py-1 text-left">Employee Name</th>
                        @for ($d = 1; $d <= $daysInMonth; $d++)
                            <th class="border px-2 py-1 text-center">{{ $d }}</th>
                        @endfor
                        <th class="border px-2 py-1 text-center">Day Hrs</th>
                        <th class="border px-2 py-1 text-center">Night Hrs</th>
                        <th class="border px-2 py-1 text-center">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employees as $index => $employee)
                        @php
                            $dayTotal = 0;
                            $nightTotal = 0;
                        @endphp
                        <tr>
                            <td class="border px-2 py-1">{{ $index + 1 }}</td>
                            <td class="border px-2 py-1">{{ $employee->name }}</td>

                            @for ($d = 1; $d <= $daysInMonth; $d++)
                                @php
                                    $dayHours = $attendances[$employee->id][$d]['day'] ?? null;
                                    $nightHours = $attendances[$employee->id][$d]['night'] ?? null;
                                    $dayTotal += is_numeric($dayHours) ? $dayHours : 0;
                                    $nightTotal += is_numeric($nightHours) ? $nightHours : 0;
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
                                        <span class="text-blue-700">D: {{ $dayTotal }}</span><br>
                                        <span class="text-purple-700">N: {{ $nightTotal }}</span>
                                </div>
                            </td>
                            <td class="border px-2 py-1 text-center text-purple-700 font-semibold">{{ $nightTotal }}
                            </td>
                            <td class="border px-2 py-1 text-center font-semibold">{{ $dayTotal + $nightTotal }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
