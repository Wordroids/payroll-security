<x-app-layout>
    <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-2xl font-semibold text-gray-900">Edit Salary Advance</h1>
            <p class="mt-2 text-sm text-gray-700">
                Update the details of the salary advance below.
            </p>

            <form action="{{ route('salary.advance.update', $salaryAdvance->id) }}" method="POST" class="mt-6">
                @csrf
                @method('PUT')
                <input type="hidden" name="employee_id" value="{{ $salaryAdvance->employee->id }}">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Employee Number -->
                    <div>
                        <label for="emp_no" class="block text-sm font-medium text-gray-700">Employee Number</label>
                        <input type="text" name="emp_no" id="emp_no"
                            value="{{ $salaryAdvance->employee->emp_no ?? old('emp_no') }}"
                            class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            readonly>
                    </div>

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="name" id="name"
                            value="{{ $salaryAdvance->employee->name ?? old('name') }}"
                            class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            readonly>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                        <input type="text" name="phone" id="phone"
                            value="{{ $salaryAdvance->employee->phone ?? old('phone') }}"
                            class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            readonly>
                    </div>

                    <!-- NIC -->
                    <div>
                        <label for="nic" class="block text-sm font-medium text-gray-700">NIC</label>
                        <input type="text" name="nic" id="nic"
                            value="{{ $salaryAdvance->employee->nic ?? old('nic') }}"
                            class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            readonly>
                    </div>

                    <!-- Advance Date -->
                    <div>
                        <label for="advance_date" class="block text-sm font-medium text-gray-700">Advance Date</label>
                        <input type="date" name="advance_date" id="advance_date"
                            value="{{ \Carbon\Carbon::parse($salaryAdvance->advance_date)->format('Y-m-d') }}"
                            class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <!-- Amount -->
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
                        <input type="number" name="amount" id="amount"
                            value="{{ old('amount', $salaryAdvance->amount) }}" step="0.01"
                            class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <!-- Reason -->
                    <div class="sm:col-span-2">
                        <label for="reason" class="block text-sm font-medium text-gray-700">Reason</label>
                        <textarea name="reason" id="reason" rows="4"
                            class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('reason', $salaryAdvance->reason) }}</textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <a href="{{ route('salary.advance') }}"
                        class="inline-flex items-center px-4 py-2 mr-4 text-sm font-medium text-gray-700 bg-white border rounded-md shadow-sm hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
