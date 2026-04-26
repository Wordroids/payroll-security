<x-app-layout>
    <div style="padding:1rem; border: 2px dashed #e5e7eb; border-radius: 0.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h1 style="font-size: 1.25rem; font-weight: 600;">ETF Form R4 Remittance</h1>

            <form method="GET" action="{{ route('epf.form-r.index') }}" class="flex gap-2">
                <select name="month" class="rounded-md border-gray-300 text-sm">
                    @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $m)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ $m }}
                        </option>
                    @endforeach
                </select>
                <select name="year" class="rounded-md border-gray-300 text-sm">
                    @foreach (range(date('Y') - 5, date('Y')) as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="bg-gray-800 text-white px-3 py-1 rounded text-sm">Filter</button>
            </form>
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin: 1.5rem 0;">
            <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-100">
                <p class="text-xs text-indigo-600 font-bold uppercase">Total ETF (3%)</p>
                <p class="text-xl font-semibold">Rs. {{ number_format($totals['total_contribution'], 2) }}</p>
            </div>
            <div class="bg-green-50 p-4 rounded-lg border border-green-100">
                <p class="text-xs text-green-600 font-bold uppercase">Total Employer EPF (12%)</p>
                <p class="text-xl font-semibold">Rs. {{ number_format($totals['total_employer_epf'], 2) }}</p>
            </div>
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                <p class="text-xs text-blue-600 font-bold uppercase">Total Employee EPF (8%)</p>
                <p class="text-xl font-semibold">Rs. {{ number_format($totals['total_employee_epf'], 2) }}</p>
            </div>
        </div>

        <div class="flex gap-2 mb-4">
            <a href="{{ route('epf.form-r.create') }}"
                class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-semibold">Add Record</a>
            <a href="{{ route('epf.form-r.pdf', ['month' => $month, 'year' => $year]) }}"
                class="bg-red-600 text-white px-4 py-2 rounded-md text-sm font-semibold">Download Form R4 PDF</a>

            <a href="{{ route('epf.form-r.bankDetails') }}"
                class="bg-gray-600 text-white px-4 py-2 rounded-md text-sm font-semibold">Bank Details</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-3 text-left text-xs font-bold text-gray-500 uppercase">Employee Name</th>
                        <th class="p-3 text-left text-xs font-bold text-gray-500 uppercase">National ID</th>
                        <th class="p-3 text-left text-xs font-bold text-gray-500 uppercase">Member No</th>
                        <th class="p-3 text-right text-xs font-bold text-gray-500 uppercase">Earnings</th>
                        <th class="p-3 text-right text-xs font-bold text-gray-500 uppercase">ETF (3%)</th>
                        <th class="p-3 text-right text-xs font-bold text-gray-500 uppercase">Action</th>
                    </tr>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @foreach ($records as $record)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-3 text-sm font-medium">{{ $record->employee->name }}</td>
                            <td class="p-3 text-sm text-gray-600">{{ $record->employee->nic }}</td>
                            <td class="p-3 text-sm">{{ $record->member_no }}</td>
                            <td class="p-3 text-sm text-right">{{ number_format($record->total_earnings, 2) }}</td>
                            <td class="p-3 text-sm text-right font-bold">
                                {{ number_format($record->etf_contribution, 2) }}</td>
                            <td class="p-3 text-sm">
                                <div class="flex justify-end items-center gap-3">
                                    <a href="{{ route('epf.form-r.edit', $record->id) }}"
                                        class="text-indigo-600 hover:text-indigo-900 font-semibold text-sm">Edit</a>

                                    <form action="{{ route('epf.form-r.destroy', $record->id) }}" method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this ETF record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 hover:text-red-900 font-semibold text-sm">Delete</button>
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
