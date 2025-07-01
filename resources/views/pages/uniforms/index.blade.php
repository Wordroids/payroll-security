<x-app-layout>
    <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-base font-semibold text-gray-900">Uniform Issuance</h1>
                    <p class="mt-2 text-sm text-gray-700">
                        @if ($showAll || !$currentMonth)
                            All uniform issuance records
                        @else
                            Uniform issuance for {{ \Carbon\Carbon::parse($currentMonth)->format('F Y') }}
                        @endif
                    </p>
                </div>
                <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                    <a href="{{ route('uniforms.create') }}"
                        class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        Issue New Uniform
                    </a>
                </div>
            </div>

            <div class="mt-8 flow-root">
                <div class="-mx-4 -my-2 sm:-mx-6 lg:-mx-8">
                    <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                        <form method="GET" action="{{ route('uniforms.index') }}" class="mb-6 flex items-end gap-4">
                            <div>
                                <label for="month" class="block text-sm font-medium text-gray-700">Filter by
                                    Month</label>
                                <input type="month" name="month" id="month" value="{{ $currentMonth }}"
                                    class="border rounded p-2 text-sm w-full" />
                            </div>
                            <div>
                                <label for="employee_id"
                                    class="block text-sm font-medium text-gray-700">Employee</label>
                                <select name="employee_id" id="employee_id" class="border rounded p-2 text-sm w-full">
                                    <option value="">All Employees</option>
                                    @foreach ($allEmployees as $employee)
                                        <option value="{{ $employee->id }}"
                                            {{ $employeeId == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700">Uniform
                                    Type</label>
                                <select name="type" id="type" class="border rounded p-2 text-sm w-full">
                                    <option value="">All Types</option>
                                    <option value="Shirt" {{ request('type') == 'Shirt' ? 'selected' : '' }}>Shirt
                                    </option>
                                    <option value="Trouser" {{ request('type') == 'Trouser' ? 'selected' : '' }}>Trouser
                                    </option>
                                    <option value="Belt" {{ request('type') == 'Belt' ? 'selected' : '' }}>Belt
                                    </option>
                                    <option value="Apalo" {{ request('type') == 'Apalo' ? 'selected' : '' }}>Apalo
                                    </option>
                                    <option value="Lanyard" {{ request('type') == 'Lanyard' ? 'selected' : '' }}>
                                        Lanyard</option>
                                    <option value="Shoes" {{ request('type') == 'Shoes' ? 'selected' : '' }}>Shoes
                                    </option>
                                </select>
                            </div>
                            <div class="pt-2">
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700">
                                    Filter
                                </button>
                                @if ($currentMonth || $employeeId || request('type'))
                                    <a href="{{ route('uniforms.index', ['show_all' => true]) }}"
                                        class="inline-flex items-center px-4 py-2 ml-2 bg-gray-200 text-gray-800 rounded text-sm hover:bg-gray-300">
                                        Clear Filter
                                    </a>
                                @endif
                            </div>
                        </form>

                        <div class="overflow-visible shadow-sm ring-1 ring-black/5 sm:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                            Emp No</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Name</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Total
                                            Amount</th>
                                        <th class="px-3 py-3.5 text-center text-sm font-semibold text-gray-900">Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @forelse ($employees as $employee)
                                        <tr>
                                            <td
                                                class="py-4 pl-4 pr-3 text-sm font-medium text-gray-900 whitespace-nowrap sm:pl-6">
                                                {{ $employee->emp_no ?? 'N/A' }}
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                {{ $employee->name ?? 'N/A' }}
                                            </td>


                                            <td
                                                class="px-3 py-4 text-sm text-gray-700 whitespace-nowrap overflow-visible">
                                                <div class="flex items-center space-x-2 relative">
                                                    <span>Rs.{{ number_format($employee->uniforms->sum('total_amount'), 2) }}</span>
                                                    @if ($employee->uniforms->count() > 0)
                                                        <svg data-tooltip-target="tooltip-uniforms-{{ $employee->id }}"
                                                            class="w-5 h-5 text-gray-500 hover:text-indigo-600 cursor-pointer transition"
                                                            xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path fill-rule="evenodd"
                                                                d="M3.559 4.544c.355-.35.834-.544 1.33-.544H19.11c.496 0 .975.194 1.33.544.356.35.559.829.559 1.331v9.25c0 .502-.203.981-.559 1.331-.355.35-.834.544-1.33.544H15.5l-2.7 3.6a1 1 0 0 1-1.6 0L8.5 17H4.889c-.496 0-.975-.194-1.33-.544A1.868 1.868 0 0 1 3 15.125v-9.25c0-.502.203-.981.559-1.331ZM7.556 7.5a1 1 0 1 0 0 2h8a1 1 0 0 0 0-2h-8Zm0 3.5a1 1 0 1 0 0 2H12a1 1 0 1 0 0-2H7.556Z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                        <div id="tooltip-uniforms-{{ $employee->id }}" role="tooltip"
                                                            class="absolute left-8 z-50 invisible w-80 px-4 py-3 text-sm font-medium text-white transition-opacity duration-300 bg-gray-800 rounded shadow-lg opacity-0 tooltip">
                                                            <ul class="space-y-3 max-h-64 overflow-y-auto">
                                                                @foreach ($employee->uniforms as $uniform)
                                                                    <li class="border-b border-gray-600 pb-2">
                                                                        <div
                                                                            class="flex justify-between items-start mb-1">
                                                                            <span
                                                                                class="block font-semibold text-indigo-300">
                                                                                Rs.{{ number_format($uniform->total_amount, 2) }}
                                                                            </span>
                                                                            <span class="block text-gray-300 text-xs">
                                                                                {{ $uniform->date->format('d M Y') }}
                                                                            </span>
                                                                        </div>
                                                                        <div class="text-gray-400 text-xs">
                                                                            <div class="mb-1">
                                                                                <span
                                                                                    class="font-medium">{{ $uniform->type }}</span>
                                                                                <div class="flex justify-between">
                                                                                    <span>{{ $uniform->quantity }} x
                                                                                        Rs.{{ number_format($uniform->unit_price, 2) }}</span>
                                                                                    <span>=
                                                                                        Rs.{{ number_format($uniform->total_amount, 2) }}</span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        @if ($uniform->notes)
                                                                            <div
                                                                                class="text-gray-400 text-xs italic mt-1">
                                                                                {{ $uniform->notes }}
                                                                            </div>
                                                                        @endif
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td
                                                class="relative py-4 pr-4 pl-3 text-right text-sm font-medium whitespace-nowrap sm:pr-6">
                                                <a href="{{ route('uniforms.employee.edit', ['employee' => $employee->id, 'month' => $currentMonth]) }}"
                                                    class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                                No uniform records found.
                                            </td>
                                        </tr>
                                    @endforelse
                                    <tr class="bg-gray-50">
                                        <td colspan="2"
                                            class="px-3 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                                            @if ($showAll || !$currentMonth)
                                                Total Uniform Costs (All Time):
                                            @else
                                                Total Uniform Costs for the Month:
                                            @endif
                                        </td>
                                        <td colspan="1" class="px-3 py-4 text-sm text-red-500 whitespace-nowrap">
                                            Rs.{{ number_format($totalUniformCosts, 2) }}
                                        </td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination -->
                        @if ($employees instanceof \Illuminate\Pagination\LengthAwarePaginator)
                            <div class="mt-4">
                                {{ $employees->appends([
                                        'month' => $currentMonth,
                                        'employee_id' => $employeeId,
                                        'type' => request('type'),
                                        'show_all' => $showAll,
                                    ])->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tooltipTriggers = document.querySelectorAll('[data-tooltip-target]');

            tooltipTriggers.forEach(trigger => {
                const tooltipId = trigger.getAttribute('data-tooltip-target');
                const tooltip = document.getElementById(tooltipId);

                trigger.addEventListener('mouseenter', () => {
                    tooltip.classList.remove('invisible', 'opacity-0');
                    tooltip.classList.add('visible', 'opacity-100');
                });

                trigger.addEventListener('mouseleave', () => {
                    tooltip.classList.add('invisible', 'opacity-0');
                    tooltip.classList.remove('visible', 'opacity-100');
                });
            });
        });
    </script>
</x-app-layout>
