<x-app-layout>
    <div class="p-6">
        <h2 class="text-lg font-semibold text-gray-900">Add Salary Advance</h2>

        <form action="{{ route('salary.advance.store') }}" method="POST" class="mt-6 space-y-6">
            @csrf

            <div>
                <label for="employee_id" class="block mb-2 text-sm font-medium text-gray-700">Employee</label>
                <select name="employee_id" id="employee_id" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="">-- Select Employee --</option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                            {{ $employee->emp_no }} - {{ $employee->name }}
                        </option>
                    @endforeach
                </select>
                @error('employee_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="advance_date" class="block mb-2 text-sm font-medium text-gray-700">Advance Date</label>
                <input type="date" name="advance_date" id="advance_date"
                    value="{{ old('advance_date', now()->format('Y-m-d')) }}"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                    required>
                @error('advance_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="amount" class="block mb-2 text-sm font-medium text-gray-700">Amount</label>
                <input type="number" name="amount" id="amount" step="0.01" min="0"
                    value="{{ old('amount') }}"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                    required>
                @error('amount')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="reason" class="block mb-2 text-sm font-medium text-gray-700">Reason</label>
                <textarea name="reason" id="reason" rows="3"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('reason') }}</textarea>
                @error('reason')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-4">
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white text-sm hover:bg-indigo-700 focus:outline-none focus:ring focus:ring-indigo-500">
                    Save Advance
                </button>
                <a href="{{ route('salary.advance') }}" class="text-sm text-gray-600 hover:underline">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
