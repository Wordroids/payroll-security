<x-app-layout>
    <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-base font-semibold text-gray-900">Edit Uniform Records</h1>
                    <p class="mt-2 text-sm text-gray-700">
                        @if ($showAll)
                            All uniform records for {{ $employee->name }}
                        @elseif(!empty($month))
                            Uniform records for {{ $employee->name }} in
                            {{ \Carbon\Carbon::parse($month)->format('F Y') }}
                        @else
                            Uniform records for {{ $employee->name }} on
                            {{ \Carbon\Carbon::parse($date ?? now()->format('Y-m-d'))->format('d F Y') }}
                        @endif
                    </p>
                </div>
                <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                    <a href="{{ route('uniforms.index') }}"
                        class="block rounded-md bg-gray-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-gray-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-600">
                        Back to Uniforms
                    </a>
                </div>
            </div>

            <div class="mt-8 flow-root">
                <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                        <form method="GET"
                            action="{{ route('uniforms.employee.edit', ['employee' => $employee->id]) }}"
                            class="mb-6 flex items-end gap-4">
                            <div>
                                <label for="date" class="block text-sm font-medium text-gray-700">Filter by Date</label>
                                <input type="date" name="date" id="date" value="{{ $date ?? '' }}"
                                    class="border rounded p-2 text-sm w-full" />
                            </div>
                            <div>
                                <label for="month" class="block text-sm font-medium text-gray-700">Filter by
                                    Month</label>
                                <input type="month" name="month" id="month" value="{{ $month ?? '' }}"
                                    class="border rounded p-2 text-sm w-full" />
                            </div>
                            <div class="pt-2">
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700">
                                    Filter
                                </button>
                                @if ($showAll || $month || ($date && $date !== now()->format('Y-m-d')))
                                    <a href="{{ route('uniforms.employee.edit', ['employee' => $employee->id]) }}"
                                        class="inline-flex items-center px-4 py-2 ml-2 bg-gray-200 text-gray-800 rounded text-sm hover:bg-gray-300">
                                        Clear Filter
                                    </a>
                                @endif
                                @if (!($showAll || $month || ($date && $date !== now()->format('Y-m-d'))))
                                    <a href="{{ route('uniforms.employee.edit', ['employee' => $employee->id, 'show_all' => true]) }}"
                                        class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 rounded text-sm hover:bg-gray-300">
                                        All Records
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
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Date</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Type</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Quantity
                                        </th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Unit Price
                                        </th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Total</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Notes</th>
                                        <th class="px-3 py-3.5 text-center text-sm font-semibold text-gray-900">Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @forelse ($uniforms as $uniform)
                                        <tr>
                                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                {{ \Carbon\Carbon::parse($uniform->date)->format('Y-m-d') }}
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-500">
                                                {{ $uniform->type }}
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-500">
                                                {{ $uniform->quantity }}
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-500">
                                                Rs.{{ number_format($uniform->unit_price, 2) }}
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                Rs.{{ number_format($uniform->total_amount, 2) }}
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-500">
                                                {{ $uniform->notes }}
                                            </td>
                                            <td class="px-3 py-4 text-sm text-right font-medium whitespace-nowrap">
                                                <button onclick="openEditModal({{ $uniform->id }})"
                                                    class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</button>
                                                <button onclick="deleteUniform({{ $uniform->id }})"
                                                    class="text-red-600 hover:text-red-800">Delete</button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                                No uniform records found.
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
                                    Total Uniform Costs (All Time) for {{ $employee->name }}:
                                @elseif(!empty($month))
                                    Total Uniform Costs for {{ $employee->name }} in
                                    {{ \Carbon\Carbon::parse($month)->format('F Y') }}:
                                @else
                                    Total Uniform Costs for {{ $employee->name }} on
                                    {{ \Carbon\Carbon::parse($date ?? now()->format('Y-m-d'))->format('d F Y') }}:
                                @endif
                                <span
                                    class="text-red-600">Rs.{{ number_format($uniforms->sum('total_amount'), 2) }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="fixed z-50 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Edit Uniform Record
                            </h3>
                            <div class="mt-4">
                                <form id="editUniformForm" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="space-y-4">
                                        <div>
                                            <label for="edit_date"
                                                class="block text-sm font-medium text-gray-700">Date</label>
                                            <input type="date" name="date" id="edit_date" required
                                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        </div>

                                        <div>
                                            <label for="edit_type"
                                                class="block text-sm font-medium text-gray-700">Type</label>
                                            <select name="type" id="edit_type" required
                                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                <option value="">Select Type</option>
                                                <option value="Shirt">Shirt</option>
                                                <option value="Trouser">Trouser</option>
                                                <option value="Belt">Belt</option>
                                                <option value="Apalo">Apalo</option>
                                                <option value="Lenyard">Lenyard</option>
                                                <option value="Shoes">Shoes</option>
                                            </select>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label for="edit_quantity"
                                                    class="block text-sm font-medium text-gray-700">Quantity</label>
                                                <input type="number" name="quantity" id="edit_quantity"
                                                    min="1" required
                                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            </div>
                                            <div>
                                                <label for="edit_unit_price"
                                                    class="block text-sm font-medium text-gray-700">Unit Price</label>
                                                <input type="number" step="0.01" name="unit_price"
                                                    id="edit_unit_price" min="0" required
                                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            </div>
                                        </div>

                                        <div>
                                            <label for="edit_notes"
                                                class="block text-sm font-medium text-gray-700">Notes</label>
                                            <textarea name="notes" id="edit_notes" rows="3"
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
        const baseUrl = "{{ url('/') }}";
        let currentUniformId = null;

            document.addEventListener('DOMContentLoaded', function() {
            // Handle date/month selection
            const dateInput = document.getElementById('date');
            const monthInput = document.getElementById('month');

            if (dateInput && monthInput) {
                dateInput.addEventListener('change', function() {
                    if (this.value) {
                        monthInput.value = '';
                    }
                });

                monthInput.addEventListener('change', function() {
                    if (this.value) {
                        dateInput.value = '';
                    }
                });

                @if($month)
                    dateInput.value = '';
                @endif
            }
        });
        function openEditModal(uniformId) {
            currentUniformId = uniformId;
            document.getElementById('editModal').classList.remove('hidden');

            fetch(`${baseUrl}/uniforms/uniform/${uniformId}/edit`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(async response => {
                    if (!response.ok) {
                        const error = await response.json();
                        throw new Error(error.message || 'Failed to load uniform details');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const uniformDate = new Date(data.data.date);
                        const formattedDate = uniformDate.toISOString().split('T')[0];

                        document.getElementById('edit_date').value = formattedDate;
                        document.getElementById('edit_type').value = data.data.type;
                        document.getElementById('edit_quantity').value = data.data.quantity;
                        document.getElementById('edit_unit_price').value = data.data.unit_price;
                        document.getElementById('edit_notes').value = data.data.notes || '';


                        document.getElementById('editUniformForm').action = `${baseUrl}/uniforms/uniform/${uniformId}`;
                    } else {
                        throw new Error(data.message || 'Failed to load uniform details');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert(error.message);
                    closeEditModal();
                });
        }

        function deleteUniform(uniformId) {
            if (confirm('Are you sure you want to delete this uniform record?')) {
                fetch(`${baseUrl}/uniforms/uniform/${uniformId}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(async response => {
                        const data = await response.json();
                        if (!response.ok) {
                            throw new Error(data.message || 'Delete failed');
                        }
                        return data;
                    })
                    .then(data => {
                        if (data.success) {
                            alert('Uniform record deleted successfully!');
                            window.location.reload();
                        } else {
                            throw new Error(data.message || 'Delete failed');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert(error.message);
                    });
            }
        }

        function submitEditForm() {
            const form = document.getElementById('editUniformForm');
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
                .then(async response => {
                    if (!response.ok) {
                        const error = await response.json();
                        throw new Error(error.message || 'Update failed');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        alert('Uniform record updated successfully!');
                        closeEditModal();
                        window.location.reload();
                    } else {
                        throw new Error(data.message || 'Update failed');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert(error.message);
                });
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
