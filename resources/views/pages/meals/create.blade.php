<x-app-layout>
    <div class="p-6">
        <h2 class="text-lg font-semibold text-gray-900">Add Meal Cost</h2>

        <form action="{{ route('meals.store') }}" method="POST" class="mt-6 space-y-6" id="meal-form">
            @csrf
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="employee_id" class="block mb-2 text-sm font-medium text-gray-700">Employee *</label>
                    <select name="employee_id" id="employee_id" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">-- Select Employee --</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}"
                                {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->emp_no }} - {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="date" class="block mb-2 text-sm font-medium text-gray-700">Date *</label>
                    <input type="date" name="date" id="date" value="{{ old('date', date('Y-m-d')) }}"
                        required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
            </div>

            <div id="meal-items-container" class="space-y-4">
                <!-- Initial Meal Item -->
                <div class="meal-item flex items-end gap-4">
                    <div class="flex-1">
                        <label class="block mb-2 text-sm font-medium text-gray-700">Unit Price *</label>
                        <input type="number" name="meal_items[0][unit_price]" step="0.01" min="0" required
                            class="meal-unit-price w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <div class="flex-1">
                        <label class="block mb-2 text-sm font-medium text-gray-700">Quantity *</label>
                        <input type="number" name="meal_items[0][quantity]" min="1" required
                            class="meal-quantity w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <button type="button"
                        class="remove-meal-item px-3 py-2 bg-red-500 text-white rounded-md text-sm mb-2">
                        Remove
                    </button>
                </div>
            </div>

            <button type="button" id="add-meal-item" class="px-3 py-2 bg-blue-500 text-white rounded-md text-sm">
                Add Another Meal
            </button>

            <div class="mt-4">
                <label for="notes" class="block mb-2 text-sm font-medium text-gray-700">Notes</label>
                <textarea name="notes" id="notes" rows="3"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('notes') }}</textarea>
            </div>

            <div class="flex justify-end">
                <button type="submit" id="submit-button"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Save Meal Cost
                </button>
            </div>
        </form>
    </div>

    <script>
        (function() {
            'use strict';

            let itemCount = 1;

            function initializeMealForm() {
                const container = document.getElementById('meal-items-container');
                const addButton = document.getElementById('add-meal-item');
                const form = document.getElementById('meal-form');

                if (!container || !addButton || !form) {
                    console.error('Required elements not found');
                    return;
                }

                // Add meal item function
                function addMealItem() {
                    console.log('Adding meal item...');

                    const newItem = document.createElement('div');
                    newItem.className = 'meal-item flex items-end gap-4 mt-4';
                    newItem.innerHTML = `
                        <div class="flex-1">
                            <label class="block mb-2 text-sm font-medium text-gray-700">Unit Price *</label>
                            <input type="number" name="meal_items[${itemCount}][unit_price]" step="0.01" min="0" required
                                   class="meal-unit-price w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <div class="flex-1">
                            <label class="block mb-2 text-sm font-medium text-gray-700">Quantity *</label>
                            <input type="number" name="meal_items[${itemCount}][quantity]" min="1" required
                                   class="meal-quantity w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <button type="button" class="remove-meal-item px-3 py-2 bg-red-500 text-white rounded-md text-sm mb-2">
                            Remove
                        </button>
                    `;

                    container.appendChild(newItem);
                    itemCount++;

                    const newInputs = newItem.querySelectorAll('input[type="number"]');
                    newInputs.forEach(input => {
                        input.addEventListener('keydown', preventEnterSubmission);
                    });
                }

                // Remove meal item function
                function removeMealItem(button) {
                    console.log('Removing meal item...');

                    const mealItem = button.closest('.meal-item');
                    const items = container.querySelectorAll('.meal-item');

                    if (items.length > 1) {
                        mealItem.remove();
                        reindexMealItems();
                    } else {
                        alert('You must have at least one meal item.');
                    }
                }

                // Reindex meal items
                function reindexMealItems() {
                    const items = container.querySelectorAll('.meal-item');
                    items.forEach((item, index) => {
                        const unitPriceInput = item.querySelector('input[name*="unit_price"]');
                        const quantityInput = item.querySelector('input[name*="quantity"]');

                        if (unitPriceInput) {
                            unitPriceInput.name = `meal_items[${index}][unit_price]`;
                        }
                        if (quantityInput) {
                            quantityInput.name = `meal_items[${index}][quantity]`;
                        }
                    });

                    itemCount = items.length;
                }


                function preventEnterSubmission(e) {
                    if (e.key === 'Enter' || e.keyCode === 13) {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }
                }


                addButton.onclick = function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    addMealItem();
                    return false;
                };

                container.onclick = function(e) {
                    if (e.target && e.target.classList.contains('remove-meal-item')) {
                        e.preventDefault();
                        e.stopPropagation();
                        removeMealItem(e.target);
                        return false;
                    }
                };


                const allInputs = form.querySelectorAll('input, select');
                allInputs.forEach(input => {
                    input.addEventListener('keydown', preventEnterSubmission);
                    input.addEventListener('keypress', preventEnterSubmission);
                });


                form.addEventListener('submit', function(e) {
                    console.log('Form is being submitted...');
                    const submitButton = document.getElementById('submit-button');
                    submitButton.disabled = true;
                    submitButton.textContent = 'Saving...';
                });

                console.log('Meal form initialized successfully');
            }


            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initializeMealForm);
            } else {
                initializeMealForm();
            }
        })();
    </script>
</x-app-layout>
