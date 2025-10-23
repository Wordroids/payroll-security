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

                // If search is empty, show all rows
                if (searchTerm === '') {
                    tableRows.forEach(row => {
                        row.style.display = '';
                    });
                    // Hide no results message
                    const noResultsMessage = document.getElementById('noResultsMessage');
                    if (noResultsMessage) {
                        noResultsMessage.style.display = 'none';
                    }
                    return;
                }

                // Filter rows based on search term
                let hasResults = false;
                tableRows.forEach(row => {
                    // Skip the "no employees found" row
                    if (row.querySelector('td[colspan]')) {
                        return;
                    }

                    const fixedCells = row.querySelectorAll('.fixed-columns td');
                    const scrollableCells = row.querySelectorAll('.scrollable-columns td');
                    let rowText = '';

                    // Collect text from fixed columns
                    fixedCells.forEach(cell => {
                        rowText += cell.textContent.toLowerCase() + ' ';
                    });

                    // Collect text from scrollable columns
                    scrollableCells.forEach((cell, index) => {
                        if (index < scrollableCells.length - 1) {
                            rowText += cell.textContent.toLowerCase() + ' ';
                        }
                    });

                    // Check if row contains the search term
                    if (rowText.includes(searchTerm)) {
                        row.style.display = '';
                        hasResults = true;
                    } else {
                        row.style.display = 'none';
                    }
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
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-base font-semibold text-gray-900">Salaries</h1>
                </div>
            </div>
            <div class="mt-4 sm:mt-0 sm:ml-4">
                <form action="{{ route('salaries.download-all-slips') }}" method="GET"
                    class="flex items-center space-x-2">
                    <div>
                        <label for="month" class="sr-only">Select Month</label>
                        <input type="month" name="month" id="month" value="{{ now()->format('Y-m') }}"
                            class="rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            required>
                    </div>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Download All Slips
                    </button>
                </form>
            </div>

            <!-- Search Bar -->
            <div class="search-container mt-4">
                <div class="relative">
                    <svg class="search-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input
                        type="text"
                        id="searchInput"
                        class="search-input"
                        placeholder="Search by Emp No, Name, Rank, Address, NIC, Date of Birth, or Date of Hire..."
                    >
                </div>
            </div>
        </div>
        <div class="mt-8" style="height: calc(100vh - 170px); display: flex; flex-direction: column;">
                <div class="flex-1 overflow-auto relative">
                    <div class="absolute top-0 left-0 right-0 bottom-0 overflow-auto">
                    <!-- No Results Message (initially hidden) -->
                    <div id="noResultsMessage" class="no-results" style="display: none;">
                        <p>No employees found matching your search criteria.</p>
                    </div>

                     <div class="table-container">
                            <!-- Fixed columns (Emp No and Name) -->
                            <div class="fixed-columns">
                                <table class="min-w-full divide-y divide-gray-300">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-gray-900 sm:pl-6 sticky top-0 z-30 bg-gray-50">Emp No</th>
                                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 sticky top-0 z-30 bg-gray-50">Name</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        @forelse ($employees as $employee)
                                            <tr>
                                                <td class="py-4 pr-3 pl-4 text-sm font-medium text-gray-900 whitespace-nowrap sm:pl-6 bg-white">
                                                    {{ $employee->emp_no }}
                                                </td>
                                                <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap bg-white">
                                                    {{ $employee->name }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="px-6 py-4 text-center text-sm text-gray-500 bg-white">
                                                    No employees found.
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
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 sticky top-0 z-10 bg-gray-50">Rank</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 sticky top-0 z-10 bg-gray-50">Address</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 sticky top-0 z-10 bg-gray-50">NIC</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 sticky top-0 z-10 bg-gray-50">Date of Birth</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 sticky top-0 z-10 bg-gray-50">Date of Hire</th>
                                        <th class=" py-3.5 pr-4 pl-3 text-left text-sm font-semibold text-gray-900 sticky top-0 z-10 bg-gray-50 sm:pr-6">
                                            <span>Action</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @forelse ($employees as $employee)
                                        <tr>
                                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                {{ $employee->rank }}
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                {{ $employee->address }}
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                {{ $employee->nic }}
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                {{ \Carbon\Carbon::parse($employee->date_of_birth)->format('Y-m-d') }}
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                {{ \Carbon\Carbon::parse($employee->date_of_hire)->format('Y-m-d') }}
                                            </td>
                                            <td class="relative py-4 pr-4 pl-3 text-left text-sm font-medium whitespace-nowrap sm:pr-6">
                                                <a href="{{ route('salaries.show', $employee->id) }}"
                                                   class="text-indigo-600 hover:text-indigo-900">View Salary Log</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                                No employees found.
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
</x-app-layout>
