<x-app-layout>
     <style>
        /* hover effect to all table rows */
        table tbody tr:hover {
            background-color: #f3f4f6;
            cursor: pointer;
        }

        table tbody tr:hover td {
            color: #111827;
        }

        /* Search bar styles */
        .search-container {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 0.75rem 1rem;
            padding-left: 2.5rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .search-input:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
        }

        .no-results {
            text-align: center;
            padding: 2rem;
            color: #6b7280;
        }

        /* table layout */
        .table-container {
            display: flex;
            min-width: max-content;
        }

        .fixed-columns {
            position: sticky;
            left: 0;
            z-index: 20;
            background-color: white;
        }

        .scrollable-columns {
            flex: 1;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const tableRows = document.querySelectorAll('table tbody tr');

            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();


                if (searchTerm === '') {
                    tableRows.forEach(row => {
                        row.style.display = '';
                    });

                    const noResultsMessage = document.getElementById('noResultsMessage');
                    if (noResultsMessage) {
                        noResultsMessage.style.display = 'none';
                    }
                    return;
                }

                // Filter rows based on search term
                let hasResults = false;
                let rowIndex = 0;

                tableRows.forEach(row => {

                    if (row.querySelector('td[colspan]')) {
                        return;
                    }


                    const fixedRow = document.querySelector(
                        `.fixed-columns tbody tr:nth-child(${rowIndex + 1})`);
                    const scrollableRow = document.querySelector(
                        `.scrollable-columns tbody tr:nth-child(${rowIndex + 1})`);

                    if (fixedRow && scrollableRow) {
                        const fixedCells = fixedRow.querySelectorAll('td');
                        const scrollableCells = scrollableRow.querySelectorAll('td');
                        let rowText = '';

                        // Collect text from fixed columns
                        fixedCells.forEach(cell => {
                            rowText += cell.textContent.toLowerCase() + ' ';
                        });

                        // Collect text from scrollable columns
                        scrollableCells.forEach(cell => {
                            rowText += cell.textContent.toLowerCase() + ' ';
                        });

                        // Check if row contains the search term
                        if (rowText.includes(searchTerm)) {
                            fixedRow.style.display = '';
                            scrollableRow.style.display = '';
                            hasResults = true;
                        } else {
                            fixedRow.style.display = 'none';
                            scrollableRow.style.display = 'none';
                        }
                    }

                    rowIndex++;
                });

                // Show/hide no results message
                const noResultsMessage = document.getElementById('noResultsMessage');
                if (noResultsMessage) {
                    noResultsMessage.style.display = hasResults ? 'none' : 'block';
                }
            });
        });
    </script>

    <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h1 class="text-base font-semibold text-gray-900 mb-4">Salary Overview</h1>

                <form method="GET" action="{{ route('salaries.overview') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="month" class="block text-sm font-medium text-gray-700">Month</label>
                        <input type="month" name="month" id="month" value="{{ $month }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="employee_id" class="block text-sm font-medium text-gray-700">Employee</label>
                        <select name="employee_id" id="employee_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">All Employees</option>
                            @foreach($allEmployees as $emp)
                            <option value="{{ $emp->id }}" {{ $selectedEmployee == $emp->id ? 'selected' : '' }}>
                                {{ $emp->name }} ({{ $emp->emp_no }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-500 focus:outline-none">
                            Filter
                        </button>

                        <a href="{{ route('salaries.overview.pdf', request()->query()) }}"
                            class="ml-2 inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-white hover:bg-red-500 focus:outline-none">
                            Download PDF
                        </a>
                    </div>
                </form>
            </div>

            <!-- Search Bar -->
            <div class="search-container">
                <div class="relative">
                    <svg class="search-icon" width="20" height="20" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" id="searchInput" class="search-input"
                        placeholder="Search by Emp No, Name, or EPF/ETF status...">
                </div>
            </div>

            <div class="mt-6" style="height: calc(100vh - 280px); display: flex; flex-direction: column;">
                <div class="flex-1 overflow-auto relative">
                    <div class="absolute inset-0 overflow-auto">
                        <!-- No Results Message (initially hidden) -->
                        <div id="noResultsMessage" class="no-results" style="display: none;">
                            <p>No salary data found matching your search criteria.</p>
                        </div>

                        <div class="table-container">
                            <!-- Fixed columns (Emp No, Name, and EPF/ETF) -->
                            <div class="fixed-columns">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-gray-900 sm:pl-6 sticky top-0 z-30 bg-gray-50">Emp No</th>
                                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 sticky top-0 z-30 bg-gray-50">Name</th>
                                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 sticky top-0 z-30 bg-gray-50">EPF/ETF</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @forelse($salaryData as $data)
                                    <tr>
                                        <td class="py-4 pr-3 pl-4 text-sm font-medium text-gray-900 whitespace-nowrap sm:pl-6 bg-white">
                                            {{ $data['employee']->emp_no }}
                                        </td>
                                        <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap bg-white">
                                            {{ $data['employee']->name }}
                                        </td>
                                         <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap bg-white">
                                            {{ $data['employee']->include_epf_etf ? 'Yes' : 'No' }}
                                        </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500 bg-white">
                                                No salary data found.
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Scrollable columns -->
                            <div class="scrollable-columns">
                                <table class="min-w-full divide-y divide-gray-300">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 sticky top-0 z-10 bg-gray-50">Shifts</th>
                                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 sticky top-0 z-10 bg-gray-50">Basic</th>
                                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 sticky top-0 z-10 bg-gray-50">OT Earnings</th>
                                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 sticky top-0 z-10 bg-gray-50">Special OT Earnings</th>
                                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 sticky top-0 z-10 bg-gray-50">Shift Earnings</th>
                                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 sticky top-0 z-10 bg-gray-50">Att. Bonus</th>
                                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 sticky top-0 z-10 bg-gray-50">Perf. Allow</th>
                                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 sticky top-0 z-10 bg-gray-50">Other Allow</th>
                                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 sticky top-0 z-10 bg-gray-50">Sub Total</th>
                                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 sticky top-0 z-10 bg-gray-50">Gross Pay</th>
                                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 sticky top-0 z-10 bg-gray-50">Salary Adv</th>
                                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 sticky top-0 z-10 bg-gray-50">Meals</th>
                                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 sticky top-0 z-10 bg-gray-50">Uniforms</th>
                                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 sticky top-0 z-10 bg-gray-50">EPF 8%</th>
                                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 sticky top-0 z-10 bg-gray-50">Total Deductions</th>
                                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 sticky top-0 z-10 bg-gray-50">Net Pay</th>
                                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 sticky top-0 z-10 bg-gray-50">EPF 12%</th>
                                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 sticky top-0 z-10 bg-gray-50">ETF 3%</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        @forelse($salaryData as $data)
                                        <tr>
                                        <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ number_format($data['total_shifts'], 2) }}
                                        </td>
                                        <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ number_format($data['basic'], 2) }}
                                        </td>
                                        <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ number_format($data['ot_earnings'], 2) }}
                                        </td>
                                        <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ number_format($data['special_ot_earnings'], 2) }}
                                        </td>
                                        <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ number_format($data['totalShiftEarning'], 2) }}
                                        </td>
                                        <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ number_format($data['attendance_bonus'], 2) }}
                                        </td>
                                        <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ number_format($data['performance_allowance'], 2) }}
                                        </td>
                                        <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ number_format($data['other_allowances'], 2) }}
                                        </td>
                                        <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ number_format($data['sub_total'], 2) }}
                                        </td>
                                        <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ number_format($data['gross_pay'], 2) }}
                                        </td>


                                        <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ number_format($data['salary_advance'], 2) }}
                                        </td>
                                        <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ number_format($data['meal_deductions'], 2) }}
                                        </td>
                                        <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ number_format($data['uniform_deductions'], 2) }}
                                        </td>
                                        <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ $data['employee']->include_epf_etf ? number_format($data['epf_deduct_employee'], 2) : 'Excluded' }}
                                        </td>
                                        <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ number_format($data['total_deductions'], 2) }}
                                        </td>
                                        <td class="px-3 py-4 text-sm font-medium text-green-600 whitespace-nowrap">
                                            {{ number_format($data['net_pay'], 2) }}
                                        </td>
                                        <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ $data['employee']->include_epf_etf ? number_format($data['epf_employee'], 2) : 'Excluded' }}
                                        </td>
                                        <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ $data['employee']->include_epf_etf ? number_format($data['etf_employee'], 2) : 'Excluded' }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="14" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No salary data found for the selected filters.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
