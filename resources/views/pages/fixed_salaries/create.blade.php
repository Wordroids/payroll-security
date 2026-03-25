<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Add Fixed Salary</h2>
                <a href="{{ route('fixed-salaries.index') }}" class="text-sm text-indigo-600 hover:underline">Back to
                    List</a>
            </div>

            <form action="{{ route('fixed-salaries.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Position --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Position</label>
                        <select name="position" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach ($positions as $pos)
                                <option value="{{ $pos }}">{{ $pos }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Employee Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Employee Name</label>
                        <input type="text" name="employee_name" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div class="col-span-full border-b border-gray-100 pb-2 mt-4">
                        <h3 class="font-semibold text-gray-600">Earnings</h3>
                    </div>

                    {{-- Basic Salary --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Basic Salary (Rs.)</label>
                        <input type="number" step="0.01" name="basic_salary" value="0"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    {{-- Fuel Allowance --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fuel Allowance (Rs.)</label>
                        <input type="number" step="0.01" name="fuel_allowance" value="0"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    {{-- Transport Allowance --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Transport Allowance (Rs.)</label>
                        <input type="number" step="0.01" name="transport_allowance" value="0"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    {{-- Other Allowance --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Other Allowance (Rs.)</label>
                        <input type="number" step="0.01" name="other_allowances" value="0"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div class="col-span-full border-b border-gray-100 pb-2 mt-4">
                        <h3 class="font-semibold text-red-600">Deductions</h3>
                    </div>

                    {{-- EPF --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">EPF Deduction (Rs.)</label>
                        <input type="number" step="0.01" name="epf_deduction" value="0"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    {{-- Payee Tax --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Payee Tax (Rs.)</label>
                        <input type="number" step="0.01" name="payee_tax" value="0"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    {{-- Other Deductions --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Other Deductions (Rs.)</label>
                        <input type="number" step="0.01" name="other_deductions" value="0"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
