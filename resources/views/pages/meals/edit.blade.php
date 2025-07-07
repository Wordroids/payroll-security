<x-app-layout>
    <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-base font-semibold text-gray-900">Edit Meal Costs</h1>
                    <p class="mt-2 text-sm text-gray-700">
                        @if ($showAll)
                            All meal costs for {{ $employee->name }}
                        @elseif(!empty($month))
                            Meals records for {{ $employee->name }} in
                            {{ \Carbon\Carbon::parse($month)->format('F Y') }}
                        @else
                            Meals records for {{ $employee->name }} on
                            {{ \Carbon\Carbon::parse($date ?? now()->format('Y-m-d'))->format('d F Y') }}
                        @endif
                    </p>
                </div>
                <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                    <a href="{{ route('meals.index') }}"
                        class="block rounded-md bg-gray-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-gray-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-600">
                        Back to Meals
                    </a>
                </div>
            </div>

            <div class="mt-8 flow-root">
                <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                        <form method="GET" action="{{ route('meals.employee.edit', ['employee' => $employee->id]) }}"
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
                                    <a href="{{ route('meals.employee.edit', ['employee' => $employee->id]) }}"
                                        class="inline-flex items-center px-4 py-2 ml-2 bg-gray-200 text-gray-800 rounded text-sm hover:bg-gray-300">
                                        Clear Filter
                                    </a>
                                @endif
                                @if (!($showAll || $month || ($date && $date !== now()->format('Y-m-d'))))
                                    <a href="{{ route('meals.employee.edit', ['employee' => $employee->id, 'show_all' => true]) }}"
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
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Items</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Total</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Notes</th>
                                        <th class="px-3 py-3.5 text-center text-sm font-semibold text-gray-900">Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @forelse ($meals as $meal)
                                        <tr>
                                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                {{ \Carbon\Carbon::parse($meal->date)->format('Y-m-d') }}
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-500">
                                                @foreach ($meal->meal_items as $item)
                                                    <div class="mb-1">
                                                        {{ $item['quantity'] }} x
                                                        Rs.{{ number_format($item['unit_price'], 2) }}
                                                    </div>
                                                @endforeach
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                Rs.{{ number_format($meal->total_amount, 2) }}
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-500">
                                                {{ $meal->notes }}
                                            </td>
                                            <td class="px-3 py-4 text-sm text-right font-medium whitespace-nowrap">
                                                <button onclick="openEditModal({{ $meal->id }})"
                                                    class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</button>
                                                <button onclick="deleteMeal({{ $meal->id }})"
                                                    class="text-red-600 hover:text-red-800">Delete</button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                                No meal records found.
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
                                    Total Meal Costs (All Time) for {{ $employee->name }}:
                                @elseif(!empty($month))
                                    Total Meals Costs for {{ $employee->name }} in
                                    {{ \Carbon\Carbon::parse($month)->format('F Y') }}:
                                @else
                                    Total Meals Costs for {{ $employee->name }} on
                                    {{ \Carbon\Carbon::parse($date ?? now()->format('Y-m-d'))->format('d F Y') }}:
                                @endif
                                <span
                                    class="text-red-600">Rs.{{ number_format($meals->sum('total_amount'), 2) }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit -->
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
                                Edit Meal Record
                            </h3>
                            <div class="mt-4">
                                <form id="editMealForm" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="space-y-4">
                                        <div>
                                            <label for="edit_date"
                                                class="block text-sm font-medium text-gray-700">Date</label>
                                            <input type="date" name="date" id="edit_date" required
                                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        </div>

                                        <div id="mealItemsContainer">

                                        </div>

                                        <button type="button" onclick="addMealItem()"
                                            class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-5 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Add Item
                                        </button>

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
        let currentMealId = null;

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
        function openEditModal(mealId) {
            currentMealId = mealId;
            document.getElementById('editModal').classList.remove('hidden');

            fetch(`${baseUrl}/meals/${mealId}/edit`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(async response => {
                    if (!response.ok) {
                        const error = await response.json();
                        throw new Error(error.message || 'Failed to load meal details');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {

                        const mealDate = new Date(data.data.date);
                        const formattedDate = mealDate.toISOString().split('T')[0];

                        document.getElementById('edit_date').value = formattedDate;
                        document.getElementById('edit_notes').value = data.data.notes || '';


                        const container = document.getElementById('mealItemsContainer');
                        container.innerHTML = '';

                        data.data.meal_items.forEach((item, index) => {
                            addMealItem(item.unit_price, item.quantity, index);
                        });

                        document.getElementById('editMealForm').action = `${baseUrl}/meals/${mealId}`;
                    } else {
                        throw new Error(data.message || 'Failed to load meal details');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert(error.message);
                    closeEditModal();
                });
        }

        function deleteMeal(mealId) {
            if (confirm('Are you sure you want to delete this meal record?')) {
                fetch(`${baseUrl}/meals/${mealId}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(async response => {
                        if (!response.ok) {
                            const error = await response.json();
                            throw new Error(error.message || 'Delete failed');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            alert('Meal deleted successfully!');
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

        function addMealItem(unitPrice = '', quantity = '', index = null) {
            const container = document.getElementById('mealItemsContainer');
            const itemIndex = index !== null ? index : container.children.length;

            const itemDiv = document.createElement('div');
            itemDiv.className = 'meal-item border rounded p-3 mb-3';
            itemDiv.innerHTML = `
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Unit Price</label>
                <input type="number" step="0.01" name="meal_items[${itemIndex}][unit_price]"
                    value="${unitPrice}" required
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Quantity</label>
                <input type="number" name="meal_items[${itemIndex}][quantity]"
                    value="${quantity}" min="1" required
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
        </div>
        <button type="button" onclick="this.parentNode.remove()"
            class="mt-2 inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200">
            Remove Item
        </button>
    `;
            container.appendChild(itemDiv);
        }

        function submitEditForm() {
            const form = document.getElementById('editMealForm');
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
                        alert('Meal updated successfully!');
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
