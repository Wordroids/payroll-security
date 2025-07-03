<x-app-layout>
    <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-base font-semibold text-gray-900">Edit Salary Advances</h1>
                    <p class="mt-2 text-sm text-gray-700">
                        @if ($showAll)
                            All salary advances for {{ $employee->name }}
                        @elseif(!empty($month))
                            Salary advances for {{ $employee->name }} in
                            {{ \Carbon\Carbon::parse($month)->format('F Y') }}
                        @else
                            Salary advances for {{ $employee->name }} on
                            {{ \Carbon\Carbon::parse($date ?? now()->format('Y-m-d'))->format('d F Y') }}
                        @endif
                    </p>
                </div>
                <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                    <a href="{{ route('salary.advance') }}"
                        class="block rounded-md bg-gray-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-gray-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-600">
                        Back to Advances
                    </a>
                </div>
            </div>

            <div class="mt-8 flow-root">
                <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                        <form method="GET" action="{{ route('salary.advance.edit', ['employee' => $employee->id]) }}"
                            class="mb-6 flex items-end gap-4">
                            <div>
                                <label for="date" class="block text-sm font-medium text-gray-700">Filter by
                                    Date</label>
                                <input type="date" name="date" id="date" value="{{ $date ?? '' }}"
                                    class="border rounded p-2 text-sm w-full" />
                            </div>
                            <div>
                                <label for="month" class="block text-sm font-medium text-gray-700">Or by
                                    Month</label>
                                <input type="month" name="month" id="month" value="{{ $month ?? '' }}"
                                    class="border rounded p-2 text-sm w-full" />
                            </div>
                            <div class="flex gap-2 pt-2">
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700">
                                    Filter
                                </button>
                                @if ($showAll || $month || ($date && $date !== now()->format('Y-m-d')))
                                    <a href="{{ route('salary.advance.edit', ['employee' => $employee->id]) }}"
                                        class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 rounded text-sm hover:bg-gray-300">
                                        Show Today
                                    </a>
                                @endif

                                @if (!($showAll || $month || ($date && $date !== now()->format('Y-m-d'))))
                                    <a href="{{ route('salary.advance.edit', ['employee' => $employee->id, 'show_all' => true]) }}"
                                        class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 rounded text-sm hover:bg-gray-300">
                                        Show All
                                    </a>
                                @endif
                            </div>
                        </form>

                        <div class="bg-white shadow-sm ring-1 ring-black/5 sm:rounded-lg mb-8 p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Employee Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Employee ID</p>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->emp_no }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Name</p>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->name }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="overflow-hidden shadow-sm ring-1 ring-black/5 sm:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Amount
                                        </th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Advance
                                            Date</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Reason
                                        </th>
                                        <th class="px-3 py-3.5 text-center text-sm font-semibold text-gray-900">Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @forelse ($advances as $advance)
                                        <tr>
                                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                Rs.{{ number_format($advance->amount, 2) }}
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                {{ \Carbon\Carbon::parse($advance->advance_date)->format('Y-m-d') }}
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-500">
                                                {{ $advance->reason }}
                                            </td>
                                            <td class="px-3 py-4 text-sm text-right font-medium whitespace-nowrap">
                                                <button onclick="openEditModal({{ $advance->id }})"
                                                    class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</button>
                                                <form action="{{ route('salary.advance.destroy', $advance->id) }}"
                                                    method="POST" class="inline-block"
                                                    onsubmit="return confirm('Are you sure you want to delete this advance?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-600 hover:text-red-800">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                                No advances found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Total section -->
                        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                            <p class="text-sm font-medium text-gray-900">
                                @if ($showAll)
                                    Total Advances (All Time) for {{ $employee->name }}:
                                @elseif(!empty($month))
                                    Total Advances for {{ $employee->name }} in
                                    {{ \Carbon\Carbon::parse($month)->format('F Y') }}:
                                @else
                                    Total Advances for {{ $employee->name }} on
                                    {{ \Carbon\Carbon::parse($date ?? now()->format('d F Y'))}}:
                                @endif
                                <span class="text-red-600">Rs.{{ number_format($advances->sum('amount'), 2) }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="editModal" class="fixed z-50 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <!-- Modal content -->
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Edit Advance
                            </h3>
                            <div class="mt-4">
                                <form id="editAdvanceForm" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="_method" value="PUT">
                                    <div class="space-y-4">
                                        <div>
                                            <label for="edit_amount"
                                                class="block text-sm font-medium text-gray-700">Amount</label>
                                            <input type="number" step="0.01" name="amount" id="edit_amount"
                                                required
                                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        </div>
                                        <div>
                                            <label for="edit_advance_date"
                                                class="block text-sm font-medium text-gray-700">Advance Date</label>
                                            <input type="date" name="advance_date" id="edit_advance_date" required
                                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        </div>
                                        <div>
                                            <label for="edit_reason"
                                                class="block text-sm font-medium text-gray-700">Reason</label>
                                            <textarea name="reason" id="edit_reason" rows="3"
                                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="submitEditForm()"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Save
                    </button>
                    <button type="button" onclick="closeEditModal()"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Base URL setup
        const baseUrl = "{{ url('/') }}";
        let currentAdvanceId = null;

        function openEditModal(advanceId) {
            currentAdvanceId = advanceId;
            document.getElementById('editModal').classList.remove('hidden');

            fetch(`${baseUrl}/salary-advance/${advanceId}/edit`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        document.getElementById('edit_amount').value = data.data.amount;

                        const advanceDate = data.data.advance_date;
                        document.getElementById('edit_advance_date').value =
                            advanceDate.includes(' ') ? advanceDate.split(' ')[0] : advanceDate;

                        document.getElementById('edit_reason').value = data.data.reason || '';

                        const form = document.getElementById('editAdvanceForm');
                        form.action = `${baseUrl}/salary-advance/${advanceId}`;
                    } else {
                        throw new Error(data.error || 'Failed to load advance details');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading advance details: ' + error.message);
                    closeEditModal();
                });
        }
        //submit edit form
        function submitEditForm() {
            const form = document.getElementById('editAdvanceForm');
            const formData = new FormData(form);

            fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.message || 'Update failed');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        closeEditModal();
                        window.location.reload();
                    } else {
                        throw new Error(data.error || 'Update failed');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating advance: ' + error.message);
                });
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
        document.getElementById('date')?.addEventListener('change', function() {
            document.getElementById('month').value = '';
        });
        document.getElementById('month')?.addEventListener('change', function() {
            document.getElementById('date').value = '';
        });
        // Initialize modal close on outside click
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('editModal');
            modal?.addEventListener('click', (e) => {
                if (e.target === modal) closeEditModal();
            });
        });
    </script>
</x-app-layout>
