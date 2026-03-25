<x-app-layout>
    <div class="max-w-6xl mx-auto py-10 sm:px-6 lg:px-8 space-y-8">
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-semibold text-gray-700 mb-6">Salary Settings</h2>

            <form action="{{ route('salary-settings.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-2 bg-blue-50 p-4 rounded-md border border-blue-100">
                        <label for="applicable_date" class="block text-sm font-bold text-blue-800">
                            Effective Starting From (Month & Year)
                        </label>
                        <input type="month" name="applicable_date" id="applicable_date"
                            value="{{ old('applicable_date', now()->format('Y-m')) }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            required>
                        <p class="mt-1 text-xs text-blue-600">Calculations from this month onwards will use these values until a newer setting is found.</p>
                    </div>

                <div>
                    <label for="default_basic_salary" class="block text-sm font-medium text-gray-700">
                        Default Basic Salary (Rs.)
                    </label>
                    <input type="number" name="default_basic_salary" id="default_basic_salary" step="0.01"
                        value="{{ old('default_basic_salary', $settings->default_basic_salary ?? '') }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        required>
                </div>

                <div>
                    <label for="default_attendance_bonus" class="block text-sm font-medium text-gray-700">
                        Default Attendance Bonus (Rs.)
                    </label>
                    <input type="number" name="default_attendance_bonus" id="default_attendance_bonus" step="0.01"
                        value="{{ old('default_attendance_bonus', $settings->default_attendance_bonus ?? '') }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        required>
                </div>

                <div class="col-span-2">
                    <label for="special_ot_rate" class="block text-sm font-medium text-gray-700">
                        Special OT Rate (per hour)
                    </label>
                    <input type="number" name="special_ot_rate" id="special_ot_rate" step="0.01"
                        value="{{ old('special_ot_rate', $settings->special_ot_rate ?? '') }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        required>
                    <p class="mt-1 text-xs text-gray-500">Rate applied for special overtime hours (beyond 12 hours)</p>
                    </div>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center px-6 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 shadow-md">
                        Save Monthly Settings
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Historical Salary Settings</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Effective Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Basic Salary</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attendance Bonus</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Special OT Rate</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            
                            $allSettings = \App\Models\SalarySetting::orderBy('applicable_year', 'desc')
                                            ->orderBy('applicable_month', 'desc')->get();
                        @endphp
                        @foreach($allSettings as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-indigo-600">
                                {{ DateTime::createFromFormat('!m', $item->applicable_month)->format('F') }} {{ $item->applicable_year }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                Rs. {{ number_format($item->default_basic_salary, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                Rs. {{ number_format($item->default_attendance_bonus, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                Rs. {{ number_format($item->special_ot_rate, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
