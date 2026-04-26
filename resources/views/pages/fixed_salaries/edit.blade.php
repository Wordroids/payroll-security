<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Edit Fixed Salary: {{ $fixedSalary->employee_name }}</h2>
                <a href="{{ route('fixed-salaries.index') }}" class="text-sm text-indigo-600 hover:underline">Back to
                    List</a>
            </div>

            <form action="{{ route('fixed-salaries.update', $fixedSalary->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Position</label>
                        <select name="position" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach ($positions as $pos)
                                <option value="{{ $pos }}"
                                    {{ $fixedSalary->position == $pos ? 'selected' : '' }}>{{ $pos }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Employee Name</label>
                        <input type="text" name="employee_name" value="{{ $fixedSalary->employee_name }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div class="col-span-full border-b border-gray-100 pb-2 mt-4 font-semibold text-gray-600">Earnings
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Basic Salary</label>
                        <input type="number" step="0.01" name="basic_salary"
                            value="{{ $fixedSalary->basic_salary }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fuel Allowance</label>
                        <input type="number" step="0.01" name="fuel_allowance"
                            value="{{ $fixedSalary->fuel_allowance }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Transport Allowance</label>
                        <input type="number" step="0.01" name="transport_allowance"
                            value="{{ $fixedSalary->transport_allowance }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Other Allowance</label>
                        <input type="number" step="0.01" name="other_allowances"
                            value="{{ $fixedSalary->other_allowances }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>

                    <div class="col-span-full border-b border-gray-100 pb-2 mt-4 font-semibold text-red-600">Deductions
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">EPF</label>
                        <input type="number" step="0.01" name="epf_deduction"
                            value="{{ $fixedSalary->epf_deduction }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Payee Tax</label>
                        <input type="number" step="0.01" name="payee_tax" value="{{ $fixedSalary->payee_tax }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Other Deductions</label>
                        <input type="number" step="0.01" name="other_deductions"
                            value="{{ $fixedSalary->other_deductions }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                </div>

                <div class="mt-8 flex justify-between">
                    <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Update Salary
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
