<x-app-layout>
    <div class="max-w-6xl mx-auto px-6 py-10 space-y-10">
        {{-- Header with Generate Slip Button --}}
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Employee Salary Breakdown</h1>
                <p class="text-gray-500 text-sm mt-1">
                    Detailed salary breakdown for <strong>{{ $employee->name }}</strong>
                </p>
            </div>
            <button id="generateSlipBtn"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-500 focus:outline-none">
                Generate Salary Slip
            </button>
        </div>

        {{-- Employee Info --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Employee Information</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-sm text-gray-700">
                <div><strong>Emp No:</strong> {{ $employee->emp_no }}</div>
                <div><strong>Name:</strong> {{ $employee->name }}</div>
                <div><strong>Phone:</strong> {{ $employee->phone }}</div>
                <div><strong>NIC:</strong> {{ $employee->nic }}</div>
                <div><strong>Rank:</strong> {{ $employee->rank }}</div>
                <div><strong>Date of Birth:</strong>
                    {{ \Carbon\Carbon::parse($employee->date_of_birth)->format('Y-m-d') }}</div>
                <div><strong>Date of Hire:</strong>
                    {{ \Carbon\Carbon::parse($employee->date_of_hire)->format('Y-m-d') }}</div>
                <div class="col-span-full"><strong>Address:</strong> {{ $employee->address }}</div>
                <div><strong>EPF/ETF:</strong> {{ $employee->include_epf_etf ? 'Included' : 'Excluded' }}</div>
            </div>
        </div>

        {{-- Month Filter --}}
        <form method="GET" action="{{ route('salaries.show', $employee->id) }}"
            class="bg-white rounded-xl shadow p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Select Month</h2>
            <div class="flex flex-col sm:flex-row sm:items-end gap-4">
                <div class="w-full sm:w-1/3">
                    <label for="month" class="block text-sm font-medium text-gray-700">Month</label>
                    <input type="month" name="month" id="month" value="{{ $month }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div>
                    <button type="submit"
                        class="inline-flex mt-1 sm:mt-0 items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-500 focus:outline-none">
                        Filter
                    </button>
                </div>
            </div>
        </form>

        {{-- Shift Breakdown --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Assigned Sites & Shifts</h2>
            @if ($siteSummaries)
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left text-gray-700">
                        <thead class="bg-gray-100 font-semibold text-gray-800 border-b">
                            <tr>
                                <th class="px-4 py-2">Site</th>
                                <th class="px-4 py-2">Rank</th>
                                <th class="px-4 py-2">Normal Hours</th>
                                <th class="px-4 py-2">OT Hours</th>
                                <th class="px-4 py-2">Shift Rate</th>
                                <th class="px-4 py-2">Total Shifts</th>
                                <th class="px-4 py-2">Earnings</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($siteSummaries as $summary)
                                @php
                                    $siteId = $summary['site']->id;
                                    $normalHours = 0;
                                    $otHours = 0;

                                    //  hours for the site
                                    for ($d = 1; $d <= $startDate->daysInMonth; $d++) {
                                        $normalHours +=
                                            ($attendances[$employee->id][$siteId][$d]['normal_day_hours'] ?? 0) +
                                            ($attendances[$employee->id][$siteId][$d]['normal_night_hours'] ?? 0);
                                        $otHours +=
                                            ($attendances[$employee->id][$siteId][$d]['ot_day_hours'] ?? 0) +
                                            ($attendances[$employee->id][$siteId][$d]['ot_night_hours'] ?? 0);
                                    }

                                    $totalSiteHours = $normalHours + $otHours;
                                    $siteShifts = $totalSiteHours / 12;
                                    $shiftRate = $summary['rate'];
                                    $siteEarning = $siteShifts * $shiftRate;
                                @endphp
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-2">{{ $summary['site']->name }}</td>
                                    <td class="px-4 py-2">{{ $summary['rank'] }}</td>
                                    <td class="px-4 py-2">{{ number_format($normalHours, 2) }}</td>
                                    <td class="px-4 py-2">{{ number_format($otHours, 2) }}</td>
                                    <td class="px-4 py-2">Rs. {{ number_format($shiftRate, 2) }}</td>
                                    <td class="px-4 py-2">{{ number_format($siteShifts, 2) }}</td>
                                    <td class="px-4 py-2">Rs. {{ number_format($siteEarning, 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="border-t font-semibold">
                                <td class="px-4 py-2" colspan="6">Total Shift Earnings</td>
                                <td class="px-4 py-2">Rs. {{ number_format($totalShiftEarning, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-sm mt-2">No shift locations assigned.</p>
            @endif
        </div>

            {{-- Special OT --}}
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Special Overtime</h2>
     <div class="overflow-x-auto mb-4">
        <table class="min-w-full text-sm text-left text-gray-700">
            <thead class="bg-gray-100 font-semibold text-gray-800 border-b">
                <tr>
                    <th class="px-4 py-2">Site</th>
                    <th class="px-4 py-2">Day Hours</th>
                    <th class="px-4 py-2">Night Hours</th>
                    <th class="px-4 py-2">Total Hours</th>
                    <th class="px-4 py-2">Rate</th>
                    <th class="px-4 py-2">Earnings</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalSpecialOtEarnings = 0;
                    $totalSpecialOtDayHours = 0;
                    $totalSpecialOtNightHours = 0;
                @endphp

                        @if (!empty($specialOtData))
                            @foreach ($specialOtData as $siteOt)
                                @php
                                    $totalSpecialOtEarnings += $siteOt['earnings'];
                                    $totalSpecialOtDayHours += $siteOt['day_hours'];
                                    $totalSpecialOtNightHours += $siteOt['night_hours'];
                    @endphp
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2">
                                        {{ $siteOt['site']->name }}
                                        <br><span class="text-xs text-gray-500">Rank: {{ $siteOt['rank'] }}</span>
                                    </td>
                            <td class="px-4 py-2">{{ number_format($siteOt['day_hours'], 2) }}</td>
                            <td class="px-4 py-2">{{ number_format($siteOt['night_hours'], 2) }}</td>
                            <td class="px-4 py-2">{{ number_format($siteOt['total_hours'], 2) }}</td>
                            <td class="px-4 py-2">Rs. {{ number_format($siteOt['rate'], 2) }}</td>
                            <td class="px-4 py-2">Rs. {{ number_format($siteOt['earnings'], 2) }}</td>
                        </tr>
                @endforeach

                    <tr class="border-t font-semibold">
                        <td class="px-4 py-2">Total</td>
                        <td class="px-4 py-2">{{ number_format($totalSpecialOtDayHours, 2) }}</td>
                        <td class="px-4 py-2">{{ number_format($totalSpecialOtNightHours, 2) }}</td>
                        <td class="px-4 py-2">{{ number_format($specialOtHours, 2) }}</td>
                        <td class="px-4 py-2"></td>
                        <td class="px-4 py-2">Rs. {{ number_format($specialOtEarnings, 2) }}</td>
                    </tr>
                @else
                    <tr>
                        <td colspan="6" class="px-4 py-2 text-center text-gray-500">No special overtime hours recorded</td>
                    </tr>
                @endif
            </tbody>
        </table>
                </div>
            </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Basic Salary and Allowances --}}
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Basic Salary & Allowances</h2>
                <div class="space-y-2 text-sm text-gray-700">
                    <div><strong>Basic Salary:</strong> Rs. {{ number_format($employee->basic, 2) }}</div>
                    <div><strong>Attendance Bonus:</strong> Rs. {{ number_format($employee->attendance_bonus, 2) }}
                    </div>
                    <div><strong>Other Allowances:</strong> Rs. {{ number_format($otherAllowances, 2) }}</div>
            </div>
        </div>

        {{-- Earnings and Deductions --}}
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Earnings Summary</h2>
                <div class="space-y-2 text-sm text-gray-700">
                    <div><strong>Total Shift Earnings:</strong> Rs. {{ number_format($totalShiftEarning, 2) }}</div>
                    <div><strong>OT Hours:</strong> {{ $paidOtHours }} hrs</div>
                    <div><strong>OT Rate:</strong> Rs. {{ number_format($otRate, 2) }} / hr</div>
                    <div><strong>OT Earnings:</strong> Rs. {{ number_format($otEarnings, 2) }}</div>
                    <div><strong>Performance Allowance:</strong> Rs. {{ number_format($performanceAllowance, 2) }}</div>
                    <div><strong>Special OT Earnings:</strong> Rs. {{ number_format($specialOtEarnings, 2) }}</div>
                    <div><strong>Sub Total:</strong> Rs. {{ number_format($subTotal, 2) }}</div>
                    <div><strong>Gross Pay:</strong> Rs. {{ number_format($grossPay, 2) }}</div>
                </div>
                </div>
            </div>

            {{-- Deductions --}}
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Deductions Summary</h2>
                <div class="space-y-2 text-sm text-gray-700">
                @if ($employee->include_epf_etf)
                    <div><strong>EPF (Employee 12%):</strong> Rs. {{ number_format($epfEmployee, 2) }}</div>
                     <div><strong>ETF (Employer 3%):</strong> Rs. {{ number_format($etfEmployee, 2) }}</div>
                @else
                    <div><strong>EPF/ETF:</strong> Excluded from calculations</div>
                @endif
                    <div><strong>Salary Advances:</strong> Rs. {{ number_format($employee->totalSalaryAdvance, 2) }}
                    </div>
                    <div><strong>Meal Deductions:</strong> Rs. {{ number_format($mealDeductions, 2) }}</div>
                    <div><strong>Uniform Deductions:</strong> Rs. {{ number_format($uniformDeductions, 2) }}</div>
                    <div><strong>Total Deductions:</strong> Rs. {{ number_format($totalDeductions, 2) }}</div>
            </div>
        </div>

        {{-- Net Pay --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-xl font-semibold text-green-700 mb-4">Final Net Pay</h2>
            <div class="text-xl text-green-700 font-bold">Rs. {{ number_format($totalEarnings, 2) }}</div>
            <div class="mt-2 text-red-700 text-sm">
                <strong>Employer EPF/ETF (15%):</strong> Rs. {{ number_format($epfEtfEmployer, 2) }}
            </div>
        </div>

        {{-- Salary Slip Modal --}}
        <div id="salarySlipModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div id="salarySlipContent" class="p-4">
                            <!--Salary Slip Content-->
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <div class="flex space-x-2">
                            <select id="downloadFormat"
                                class="rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="pdf">PDF</option>
                            </select>
                            <button type="button" id="downloadSlipBtn"
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-500 focus:outline-none">
                                Download Slip
                            </button>
                        </div>
                        <button type="button" id="closeModalBtn"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Get all elements
                const generateBtn = document.getElementById('generateSlipBtn');
                const closeModalBtn = document.getElementById('closeModalBtn');
                const downloadSlipBtn = document.getElementById('downloadSlipBtn');
                const modal = document.getElementById('salarySlipModal');
                const salarySlipContent = document.getElementById('salarySlipContent');

                // Open modal function
                function openSalarySlipModal() {
                    // Show modal
                    modal.classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');

                    // Show loading state
                    salarySlipContent.innerHTML = `
                    <div class="flex justify-center items-center h-64">
                        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500"></div>
                        <span class="ml-3">Generating salary slip...</span>
                    </div>
                `;

                    // Fetch the salary slip content
                    fetch(`{{ route('salaries.slip.view', ['employee' => $employee->id, 'month' => $month]) }}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.text();
                        })
                        .then(html => {
                            salarySlipContent.innerHTML = html;
                        })
                        .catch(error => {
                            console.error('Error loading salary slip:', error);
                            salarySlipContent.innerHTML = `
                            <div class="text-red-500 text-center py-10">
                                <p>Error loading salary slip. Please try again.</p>
                                <button onclick="openSalarySlipModal()" class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-md">
                                    Retry
                                </button>
                            </div>
                        `;
                        });
                }

                // Close modal function
                function closeSalarySlipModal() {
                    modal.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                }

                // Download slip function
                function downloadSalarySlip() {
                    const format = document.getElementById('downloadFormat').value;
                    const signature = document.getElementById('signature')?.value || '';
                    const date = document.getElementById('receiptDate')?.value || '';

                    // Show loading state on button
                    const originalText = downloadSlipBtn.innerHTML;
                    downloadSlipBtn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing...
                `;
                    downloadSlipBtn.disabled = true;

                    // Create form to submit
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action =
                        `{{ route('salaries.slip.download', ['employee' => $employee->id, 'month' => $month]) }}`;

                    // Add CSRF token
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);

                    // Add format
                    const formatInput = document.createElement('input');
                    formatInput.type = 'hidden';
                    formatInput.name = 'format';
                    formatInput.value = format;
                    form.appendChild(formatInput);

                    // Add signature if exists
                    if (signature) {
                        const signatureInput = document.createElement('input');
                        signatureInput.type = 'hidden';
                        signatureInput.name = 'signature';
                        signatureInput.value = signature;
                        form.appendChild(signatureInput);
                    }

                    // Add date if exists
                    if (date) {
                        const dateInput = document.createElement('input');
                        dateInput.type = 'hidden';
                        dateInput.name = 'date';
                        dateInput.value = date;
                        form.appendChild(dateInput);
                    }

                    document.body.appendChild(form);
                    form.submit();

                    // Reset button after delay (in case submission fails)
                    setTimeout(() => {
                        downloadSlipBtn.innerHTML = originalText;
                        downloadSlipBtn.disabled = false;
                    }, 3000);
                }

                // Event listeners
                generateBtn.addEventListener('click', openSalarySlipModal);
                closeModalBtn.addEventListener('click', closeSalarySlipModal);
                downloadSlipBtn.addEventListener('click', downloadSalarySlip);

                // Make functions available globally if needed
                window.openSalarySlipModal = openSalarySlipModal;
                window.closeSalarySlipModal = closeSalarySlipModal;
                window.downloadSalarySlip = downloadSalarySlip;
            });
        </script>
    @endpush

    @stack('scripts')
</x-app-layout>
