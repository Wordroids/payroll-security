<x-app-layout>
    <div style="padding:1rem; border: 2px dashed #e5e7eb; border-radius: 0.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h1 style="font-size: 1.25rem; font-weight: 600;">Form C - Historical Bank Details</h1>
        </div>

        <div class="flex gap-2 mb-4 mt-4">
            <a href="{{ route('epf.form-c.bankDetails.create') }}"
                class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-semibold">Add Bank Details</a>
            <a href="{{ route('epf.form-c.index') }}"
                class="bg-red-600 text-white px-4 py-2 rounded-md text-sm font-semibold">Back to Form C List</a>
        </div>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-3 text-left text-xs font-bold text-gray-500 uppercase">Year</th>
                        <th class="p-3 text-left text-xs font-bold text-gray-500 uppercase">Month</th>
                        <th class="p-3 text-left text-xs font-bold text-gray-500 uppercase">Bank Name</th>
                        <th class="p-3 text-left text-xs font-bold text-gray-500 uppercase">Branch Name</th>
                        <th class="p-3 text-left text-xs font-bold text-gray-500 uppercase">Check No</th>
                        <th class="p-3 text-right text-xs font-bold text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @foreach ($records as $record)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-3 text-sm font-medium">{{ $record->year }}</td>
                            <td class="p-3 text-sm font-medium">{{ $record->month }}</td>
                            <td class="p-3 text-sm text-gray-600">{{ $record->bank_name }}</td>
                            <td class="p-3 text-sm">{{ $record->branch_name }}</td>
                            <td class="p-3 text-sm">{{ $record->cheque_no }}</td>
                            <td class="p-3 text-sm">
                                <div class="flex justify-end items-center gap-4">
                                    <a href="{{ route('epf.form-c.bankDetails.edit', ['month' => $record->month, 'year' => $record->year]) }}"
                                        class="text-indigo-600 hover:text-indigo-900 font-semibold">
                                        Edit
                                    </a>

                                    <form action="{{ route('epf.form-c.bankDetails.destroy', ['month' => $record->month, 'year' => $record->year]) }}"
                                        method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete Form C bank details for {{ $record->month }} {{ $record->year }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 font-semibold">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
