<x-app-layout>
    <div class="max-w-4xl mx-auto py-10 sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-semibold text-gray-700 mb-6">Salary Settings</h2>

            <form action="{{ route('salary-settings.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="default_basic_salary" class="block text-sm font-medium text-gray-700">
                        Default Basic Salary
                    </label>
                    <input type="number" name="default_basic_salary" id="default_basic_salary" step="0.01"
                        value="{{ old('default_basic_salary', $settings->default_basic_salary) }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        required>
                </div>

                <div>
                    <label for="default_attendance_bonus" class="block text-sm font-medium text-gray-700">
                        Default Attendance Bonus
                    </label>
                    <input type="number" name="default_attendance_bonus" id="default_attendance_bonus" step="0.01"
                        value="{{ old('default_attendance_bonus', $settings->default_attendance_bonus) }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        required>
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Update Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
