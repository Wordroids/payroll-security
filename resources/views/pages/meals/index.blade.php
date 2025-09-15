<x-app-layout>
    <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-base font-semibold text-gray-900">Meal Costs</h1>
                    <p class="mt-2 text-sm text-gray-700">
                        @if ($showAll)
                            Showing all Meals records
                        @elseif(!empty($filterMonth))
                           Meals records for {{ \Carbon\Carbon::parse($filterMonth)->format('F Y') }}
                        @else
                           Meals records for {{ \Carbon\Carbon::parse($currentDate)->format('d F Y') }}
                        @endif
                    </p>
                </div>
                <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                    <a href="{{ route('meals.create') }}"
                        class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        Add Meal Cost
                    </a>
                </div>
            </div>

            <div class="mt-8 flow-root">
                <div class="-mx-4 -my-2 sm:-mx-6 lg:-mx-8">
                    <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                        <form method="GET" action="{{ route('meals.index') }}" class="mb-6 flex items-end gap-4">
                           <div>
                                <label for="date" class="block text-sm font-medium text-gray-700">Filter by Date</label>
                                <input type="date" name="date" id="date"
                                    value="{{  $showAll ? '' : ($filterMonth ? '' : $currentDate )}}"
                                    class="border rounded p-2 text-sm w-full" />
                            </div>
                            <div>
                                <label for="month" class="block text-sm font-medium text-gray-700">Filter by
                                    Month</label>
                                <input type="month" name="month" id="month" value="{{ $filterMonth ?? '' }}"
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
                            <div class="pt-2">
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700">
                                    Filter
                                </button>
                                @if ($showAll || $filterMonth || ($currentDate && $currentDate !== now()->format('Y-m-d')) || $employeeId)
                                    <a href="{{ route('meals.index')}}"
                                        class="inline-flex items-center px-4 py-2 ml-2 bg-gray-200 text-gray-800 rounded text-sm hover:bg-gray-300">
                                        Clear Filter
                                    </a>
                                @endif

                               @if (!($showAll || $filterMonth || ($currentDate && $currentDate !== now()->format('Y-m-d')) || $employeeId ))
                                    <a href="{{ route('meals.index', ['show_all' => true]) }}"
                                        class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 rounded text-sm hover:bg-gray-300">
                                        All Records
                                    </a>
                                @endif
                            </div>
                        </form>

                        <div class="overflow-visible shadow-sm ring-1 ring-black/5 sm:rounded-lg">
                           <div style="height: calc(100vh - 250px); overflow: auto;">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="sticky top-0 z-10 bg-gray-50 py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                            Emp No</th>
                                        <th class="sticky top-0 z-10 bg-gray-50 py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-gray-900 sm:pl-6">Name</th>
                                        <th class="sticky top-0 z-10 bg-gray-50 py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-gray-900 sm:pl-6">Total
                                            Amount</th>
                                        <th class="sticky top-0 z-10 bg-gray-50 py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-gray-900 sm:pl-6">Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @forelse ($employees as $employee)
                                        <tr>
                                            <td
                                                class="py-4 pr-3 pl-4 text-sm font-medium text-gray-900 whitespace-nowrap sm:pl-6">
                                                {{ $employee->emp_no ?? 'N/A' }}
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                {{ $employee->name ?? 'N/A' }}
                                            </td>
                                            <td
                                                class="px-3 py-4 text-sm text-gray-700 whitespace-nowrap overflow-visible">
                                                <div class="flex items-center space-x-2 relative">
                                                    <span>Rs.{{ number_format($employee->meals->sum('amount'), 2) }}</span>
                                                    <!-- Tooltip Trigger Icon -->
                                                    <svg data-tooltip-target="tooltip-meals-{{ $employee->id }}"
                                                        class="w-5 h-5 text-gray-500 hover:text-indigo-600 cursor-pointer transition"
                                                        xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path fill-rule="evenodd"
                                                            d="M3.559 4.544c.355-.35.834-.544 1.33-.544H19.11c.496 0 .975.194 1.33.544.356.35.559.829.559 1.331v9.25c0 .502-.203.981-.559 1.331-.355.35-.834.544-1.33.544H15.5l-2.7 3.6a1 1 0 0 1-1.6 0L8.5 17H4.889c-.496 0-.975-.194-1.33-.544A1.868 1.868 0 0 1 3 15.125v-9.25c0-.502.203-.981.559-1.331ZM7.556 7.5a1 1 0 1 0 0 2h8a1 1 0 0 0 0-2h-8Zm0 3.5a1 1 0 1 0 0 2H12a1 1 0 1 0 0-2H7.556Z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    <!-- Tooltip Content -->
                                                    <div id="tooltip-meals-{{ $employee->id }}" role="tooltip"
                                                        class="absolute left-8 z-50 invisible w-80 px-4 py-3 text-sm font-medium text-white transition-opacity duration-300 bg-gray-800 rounded shadow-lg opacity-0 tooltip">
                                                        <ul class="space-y-3 max-h-64 overflow-y-auto">
                                                            @foreach ($employee->meals as $meal)
                                                                <li class="border-b border-gray-600 pb-2">
                                                                    <div class="flex justify-between items-start mb-1">
                                                                        <span
                                                                            class="block font-semibold text-indigo-300">Rs.{{ number_format($meal->amount, 2) }}</span>
                                                                        <span
                                                                            class="block text-gray-300 text-xs">{{ $meal->date->format('d M Y') }}</span>
                                                                    </div>
                                                                    @if ($meal->notes)
                                                                        <div class="text-gray-400 text-xs italic mt-1">
                                                                            {{ $meal->notes }}
                                                                        </div>
                                                                    @endif
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            </td>
                                            <td
                                                class="relative py-4 pr-4 pl-3 text-right text-sm font-medium whitespace-nowrap sm:pr-6">
                                                <a href="{{ route('meals.employee.edit', ['employee' => $employee->id, 'date' => $currentDate, 'month' => $filterMonth]) }}"
                                                    class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</>

                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                                No meal costs found.
                                            </td>
                                        </tr>
                                    @endforelse
                                    <tr>
                                        <td colspan="2"
                                            class="px-3 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                                            @if ($showAll)
                                                Total Meal Costs (All Time):
                                             @elseif($filterMonth)
                                                Total Meals Costs for {{ \Carbon\Carbon::parse($filterMonth)->format('F Y') }}:
                                            @else
                                                Total Meals Costs for {{ \Carbon\Carbon::parse($currentDate)->format('d F Y') }}:
                                            @endif
                                        </td>
                                        <td colspan="1" class="px-3 py-4 text-sm text-red-500 whitespace-nowrap">
                                            Rs.{{ number_format($totalMealCosts, 2) }}
                                        </td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        </div>
                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $employees->appends([ 'date' => $currentDate,'month' => $filterMonth, 'employee_id' => $employeeId, 'show_all' => $showAll])->links() }}
                        </div>
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
                // Date/month selection handling
                     const dateInput = document.getElementById('date');
            const monthInput = document.getElementById('month');

            if (dateInput && monthInput) {
                // Clear date when month is selected
                monthInput.addEventListener('change', function() {
                    if (this.value) {
                        dateInput.value = '';
                    }
                });

                // Clear month when date is selected
                dateInput.addEventListener('change', function() {
                    if (this.value) {
                        monthInput.value = '';
                    }
                });

                // Initialize based on current filters
                @if($filterMonth || $showAll)
                    dateInput.value = '';
                @endif
            }
        });
    </script>
</x-app-layout>
