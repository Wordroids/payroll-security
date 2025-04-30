<x-app-layout>
    <div class="p-6">
        <h2 class="text-2xl font-semibold mb-4">Monthly Attendance Entry</h2>

        <form method="GET" action="{{ route('attendances.site-entry') }}" class="flex gap-4 items-end mb-6">
            <div>
                <label for="site_id" class="text-sm text-gray-700">Select Site</label>
                <select name="site_id" id="site_id" class="border rounded w-full p-2">
                    <option value="">-- Choose Site --</option>
                    @foreach ($sites as $site)
                        <option value="{{ $site->id }}" {{ $selectedSite == $site->id ? 'selected' : '' }}>
                            {{ $site->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="month" class="text-sm text-gray-700">Select Month</label>
                <input type="month" name="month" id="month" value="{{ $selectedMonth }}"
                    class="border rounded p-2">
            </div>

            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Load</button>
        </form>

        @if ($selectedSite && $guards)
            <form method="POST" action="{{ route('attendances.site-entry.store') }}">
                @csrf
                <input type="hidden" name="site_id" value="{{ $selectedSite }}">
                <input type="hidden" name="month" value="{{ $selectedMonth }}">

                <div class="overflow-x-auto">
                    <table class="min-w-max w-full text-sm text-left border border-collapse">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-2 py-1">#</th>
                                <th class="border px-2 py-1">Guard Name</th>
                                @for ($day = 1; $day <= 31; $day++)
                                    <th class="border px-2 py-1 text-center">{{ $day }}</th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($guards as $index => $guard)
                                <tr>
                                    <td class="border px-2 py-1">{{ $index + 1 }}</td>
                                    <td class="border px-2 py-1">{{ $guard->name }}</td>
                                    @for ($day = 1; $day <= 31; $day++)
                                        @php
                                            $dayField = "attendances[{$guard->id}][{$day}][day]";
                                            $nightField = "attendances[{$guard->id}][{$day}][night]";

                                            $dayValue =
                                                old("attendances.{$guard->id}.{$day}.day") ??
                                                ($filledAttendances[$guard->id][$day]['day'] ?? '');

                                            $nightValue =
                                                old("attendances.{$guard->id}.{$day}.night") ??
                                                ($filledAttendances[$guard->id][$day]['night'] ?? '');
                                        @endphp
                                        <td class="border  px-1 py-1 text-center">
                                            <input type="number" name="{{ $dayField }}"
                                                value="{{ $dayValue }}" class="w-14 block border p-1 mb-1 text-sm"
                                                min="0" max="24" placeholder="Day">

                                            <input type="number" name="{{ $nightField }}"
                                                value="{{ $nightValue }}" class="w-14 block border p-1 text-sm"
                                                min="0" max="24" placeholder="Night">
                                        </td>
                                    @endfor


                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-500">
                        Save Attendance
                    </button>
                </div>
            </form>
        @endif
    </div>
</x-app-layout>
