<x-app-layout>
    <div class="max-w-4xl mx-auto py-10 sm:px-6 lg:px-8">
        <div style="padding:2rem; border: 2px dashed #e5e7eb; border-radius: 0.5rem; background-color: white;">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">{{ $record ? 'Edit' : 'Add' }} Bank Details for {{ $month }} {{ $year }}</h2>
                    <p class="text-sm text-gray-500">Form R4 - Act No. 46 of 1980</p>
                </div>
                <a href="{{ route('epf.form-r.index', ['month' => $month, 'year' => $year]) }}"
                    class="text-sm text-indigo-600 hover:text-indigo-900">← Back to List</a>
            </div>

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('epf.form-r.bankDetails.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Bank Name</label>
                        <input type="text" name="bank_name" value="{{ $record ? $record->bank_name : '' }}"
                            placeholder="e.g. Sampath Bank" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Branch Name</label>
                        <input type="text" name="branch_name" value="{{ $record ? $record->branch_name : '' }}"
                            placeholder="e.g. Colombo" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Check No</label>
                        <input type="text" name="cheque_no" value="{{ $record ? $record->cheque_no : '' }}"
                            placeholder="e.g. 101" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cheques Return Charges</label>
                        <input type="number" step="0.01" name="cheque_return_charges"
                            value="{{ $record ? $record->cheque_return_charges : '0.00' }}" placeholder="e.g. 171.00"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Select Month</label>
                        <select name="month"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $m)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                    {{ $m }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Year</label>
                        <input type="number" name="year" value="{{ $year }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>

                <hr class="border-gray-200">

                <div class="pt-4">
                    <button type="submit"
                        class="w-full inline-flex justify-center items-center px-4 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        Save Bank Details
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>