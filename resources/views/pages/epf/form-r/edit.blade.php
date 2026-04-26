<x-app-layout>
    <div class="max-w-4xl mx-auto py-10 sm:px-6 lg:px-8">
        <div style="padding:2rem; border: 2px dashed #e5e7eb; border-radius: 0.5rem; background-color: white;">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Edit Monthly ETF/EPF Record</h2>
                    <p class="text-sm text-gray-500">Form R4 - Act No. 46 of 1980</p>
                </div>
                <a href="{{ route('epf.form-r.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">← Back to
                    List</a>
            </div>

            <form action="{{ route('epf.form-r.update', $record->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Select Employee</label>
                        <select name="employee_id" id="employee_id" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">Choose Employee</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" data-nic="{{ $employee->nic }}"
                                    {{ $record->employee_id == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->emp_no }} - {{ $employee->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">ETF Member No</label>
                        <input type="text" name="member_no" value="{{ $record->member_no }}" placeholder="e.g. 101"
                            required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Contribution Month</label>
                        <select name="month"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $m)
                                <option value="{{ $m }}" {{ $record->month == $m ? 'selected' : '' }}>
                                    {{ $m }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Year</label>
                        <input type="number" name="year" value="{{ $record->year }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>

                <hr class="border-gray-200">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-4 rounded-lg">
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Total Earnings (Rs.)</label>
                        <input type="number" name="total_earnings" id="total_earnings" step="0.01"
                            value="{{ $record->total_earnings }}" required
                            class="mt-1 block w-full border-indigo-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm font-semibold text-lg"
                            oninput="calculateContributions()">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Surcharge (If any)</label>
                        <input type="number" name="surcharge" id="surcharge" step="0.01"
                            value="{{ $record->surcharge }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            oninput="calculateContributions()">
                    </div>

                    <div class="bg-white p-3 border rounded shadow-sm">
                        <p class="text-xs text-gray-500 uppercase">ETF Contribution (3%)</p>
                        <p class="text-lg font-bold text-indigo-600" id="display_etf">Rs. 0.00</p>
                        <input type="hidden" name="etf_contribution" id="hidden_etf">
                    </div>

                    <div class="bg-white p-3 border rounded shadow-sm">
                        <p class="text-xs text-gray-500 uppercase">Total Remittance</p>
                        <p class="text-lg font-bold text-green-600" id="display_total">Rs. 0.00</p>
                    </div>
                </div>

                <input type="hidden" name="employer_epf" id="hidden_employer_epf">
                <input type="hidden" name="employee_epf" id="hidden_employee_epf">

                <div class="pt-4">
                    <button type="submit"
                        class="w-full inline-flex justify-center items-center px-4 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        Update Monthly Record
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function calculateContributions() {
            const earnings = parseFloat(document.getElementById('total_earnings').value) || 0;
            const surcharge = parseFloat(document.getElementById('surcharge').value) || 0;

            // calculations
            const etf = (earnings * 0.03).toFixed(2);
            const employer_epf = (earnings * 0.12).toFixed(2);
            const employee_epf = (earnings * 0.08).toFixed(2);
            const total = (parseFloat(etf) + surcharge).toFixed(2);

            document.getElementById('display_etf').innerText = 'Rs. ' + etf;
            document.getElementById('display_total').innerText = 'Rs. ' + total;

            // Update Hidden Inputs
            document.getElementById('hidden_etf').value = etf;
            document.getElementById('hidden_employer_epf').value = employer_epf;
            document.getElementById('hidden_employee_epf').value = employee_epf;
        }

        document.addEventListener("DOMContentLoaded", calculateContributions);
    </script>
</x-app-layout>
