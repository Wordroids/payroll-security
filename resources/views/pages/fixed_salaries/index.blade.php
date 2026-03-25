<x-app-layout>
    <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Fixed Salaries (Executive Staff)</h1>
            <a href="{{ route('fixed-salaries.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                Add New Salary
            </a>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Position</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Basic</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Allowances</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Deductions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Net Pay</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($salaries as $s)
                    <tr>
                        <td class="px-6 py-4 font-bold">{{ $s->position }}</td>
                        <td class="px-6 py-4">{{ $s->employee_name }}</td>
                        <td class="px-6 py-4">Rs. {{ number_format($s->basic_salary, 2) }}</td>
                        <td class="px-6 py-4">Rs. {{ number_format($s->fuel_allowance + $s->transport_allowance + $s->other_allowances, 2) }}</td>
                        <td class="px-6 py-4 text-red-600">Rs. {{ number_format($s->epf_deduction + $s->payee_tax + $s->other_deductions, 2) }}</td>
                        <td class="px-6 py-4 font-bold text-green-700">Rs. {{ number_format($s->net_pay, 2) }}</td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('fixed-salaries.edit', $s->id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                            <form action="{{ route('fixed-salaries.destroy', $s->id) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Delete this record?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
