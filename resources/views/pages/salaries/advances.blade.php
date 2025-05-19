<x-app-layout>
    <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-base font-semibold text-gray-900">Salary Advances</h1>
                    <p class="mt-2 text-sm text-gray-700">
                        @if ($showAll || !$currentMonth)
                            Current salary advances.
                        @else
                            Salary advances for {{ \Carbon\Carbon::parse($currentMonth)->format('F Y') }}
                        @endif
                    </p>
                </div>
                <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                    <a href="{{ route('salary.advance.create') }}"
                        class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        Add Salary Advance
                    </a>
                </div>
            </div>

            <div class="mt-8 flow-root">
                <div class="-mx-4 -my-2 sm:-mx-6 lg:-mx-8">
                    <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                        <form method="GET" action="{{ route('salary.advance') }}" class="mb-6 flex items-end gap-4">
                            <div>
                                <label for="month" class="block text-sm font-medium text-gray-700">Filter by
                                    Month</label>
                                <input type="month" name="month" id="month" value="{{ $currentMonth }}"
                                    class="border rounded p-2 text-sm w-full" />
                            </div>
                            <div class="pt-2">
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700">
                                    Filter
                                </button>
                                @if ($currentMonth)
                                    <a href="{{ route('salary.advance', ['show_all' => true]) }}"
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
                                            class="py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                            Emp No</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Name</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Amount
                                        </th>
                                        <th class="px-3 py-3.5 text-center text-sm font-semibold text-gray-900">Actions
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
                                                    <span>Rs.{{ number_format($employee->salaryAdvances->sum('amount'), 2) }}</span>
                                                    <!-- Tooltip Trigger Icon -->
                                                    <svg data-tooltip-target="tooltip-default-{{ $employee->id }}"
                                                        data-tooltip-placement="right"
                                                        class="w-5 h-5 text-gray-500 hover:text-indigo-600 cursor-pointer transition"
                                                        xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path fill-rule="evenodd"
                                                            d="M3.559 4.544c.355-.35.834-.544 1.33-.544H19.11c.496 0 .975.194 1.33.544.356.35.559.829.559 1.331v9.25c0 .502-.203.981-.559 1.331-.355.35-.834.544-1.33.544H15.5l-2.7 3.6a1 1 0 0 1-1.6 0L8.5 17H4.889c-.496 0-.975-.194-1.33-.544A1.868 1.868 0 0 1 3 15.125v-9.25c0-.502.203-.981.559-1.331ZM7.556 7.5a1 1 0 1 0 0 2h8a1 1 0 0 0 0-2h-8Zm0 3.5a1 1 0 1 0 0 2H12a1 1 0 1 0 0-2H7.556Z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    <!-- Tooltip Content -->
                                                    <div id="tooltip-default-{{ $employee->id }}" role="tooltip"
                                                        class="absolute left-8 z-50 invisible w-64 px-4 py-3 text-sm font-medium text-white transition-opacity duration-300 bg-gray-800 rounded shadow-lg opacity-0 tooltip dark:bg-gray-700">
                                                        <ul class="space-y-2">
                                                            @foreach ($employee->salaryAdvances as $salaryAdvance)
                                                                <li class="border-b border-gray-600 pb-1">
                                                                    <span
                                                                        class="block font-semibold text-indigo-300">Rs{{ number_format($salaryAdvance->amount, 2) }}</span>
                                                                    <span
                                                                        class="block text-gray-300 text-xs">{{ \Carbon\Carbon::parse($salaryAdvance->advance_date)->format('Y-m-d') }}</span>
                                                                    <span
                                                                        class="block italic text-gray-400 text-xs">{{ $salaryAdvance->reason }}</span>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td
                                                class="relative py-4 pr-4 pl-3 text-right text-sm font-medium whitespace-nowrap sm:pr-6">
                                                <a href="{{ route('salary.advance.edit', ['employee' => $employee->id, 'month' => $currentMonth]) }}"
                                                    class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>
                                                <form action="{{ route('salary.advance.destroy', $employee->id) }}"
                                                    method="POST" class="inline-block"
                                                    onsubmit="return confirm('Are you sure you want to delete this salary advance?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-600 hover:text-red-800">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                                No salary advances found.
                                            </td>
                                        </tr>
                                    @endforelse
                                    <tr>
                                        <td colspan="2"
                                            class="px-3 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                                            @if ($showAll || !$currentMonth)
                                                Total Salary Advances (All Time):
                                            @else
                                                Total Salary Advances for the Month:
                                            @endif
                                        </td>
                                        <td colspan="1" class="px-3 py-4 text-sm text-red-500 whitespace-nowrap">
                                            Rs.{{ number_format($totalSalaryAdvances, 2) }}
                                        </td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $employees->appends(['month' => $currentMonth, 'show_all' => $showAll])->links() }}
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
        });
    </script>
</x-app-layout>
