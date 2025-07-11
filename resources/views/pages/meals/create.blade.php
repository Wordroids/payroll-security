<x-app-layout>
    <div class="p-6">
        <h2 class="text-lg font-semibold text-gray-900">Add Meal Cost</h2>

        <form action="{{ route('meals.store') }}" method="POST" class="mt-6 space-y-6" id="meal-form">
            @csrf
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="employee_id" class="block mb-2 text-sm font-medium text-gray-700">Employee *</label>
                    <select name="employee_id" id="employee_id" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">-- Select Employee --</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}"
                                {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->emp_no }} - {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="date" class="block mb-2 text-sm font-medium text-gray-700">Date *</label>
                    <input type="date" name="date" id="date" value="{{ old('date', date('Y-m-d')) }}"
                        required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
            </div>

            <div>
                <label for="amount" class="block mb-2 text-sm font-medium text-gray-700">Amount </label>
                <input type="number" name="amount" id="amount" step="0.01" min="0" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>

            <div class="mt-4">
                <label for="notes" class="block mb-2 text-sm font-medium text-gray-700">Notes</label>
                <textarea name="notes" id="notes" rows="3"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('notes') }}</textarea>
            </div>

            <div class="flex justify-end">
                <button type="submit" id="submit-button"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Save Meal Cost
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
