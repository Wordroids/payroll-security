<x-app-layout>
    <div class="max-w-4xl mx-auto py-10 sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-semibold text-gray-700 mb-6">Add Attendance Entry</h2>

            <form method="POST" action="{{ route('attendances.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @csrf

                <div>
                    <label for="employee_id" class="block text-sm font-medium text-gray-700">Employee</label>
                    <select name="employee_id" id="employee_id" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm">
                        <option value="">Select Employee</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="site_id" class="block text-sm font-medium text-gray-700">Site</label>
                    <select name="site_id" id="site_id" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm">
                        <option value="">Select Site</option>
                        @foreach($sites as $site)
                            <option value="{{ $site->id }}">{{ $site->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                    <input type="date" name="date" id="date" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm">
                </div>

                <div>
                    <label for="shift" class="block text-sm font-medium text-gray-700">Shift</label>
                    <select name="shift" id="shift" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm">
                        <option value="day">Day</option>
                        <option value="night">Night</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="worked_hours" class="block text-sm font-medium text-gray-700">Worked Hours</label>
                    <input type="number" name="worked_hours" id="worked_hours" step="0.1" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm">
                </div>

                <div class="md:col-span-2">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-500">
                        Save Attendance
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
