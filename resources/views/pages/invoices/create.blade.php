<x-app-layout>
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-xl font-semibold text-gray-800">Create New Invoice</h1>
            <a href="{{ route('invoices.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Back to Invoices</a>
        </div>

        <form method="POST" action="{{ route('invoices.store') }}" x-data="invoiceForm()">
            @csrf

            {{-- SITE + DATE --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="site_id" class="block text-sm font-medium text-gray-700 mb-1">Site</label>
                    <select name="site_id" id="site_id" required
                        class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                        <option value="">-- Select Site --</option>
                        @foreach ($sites as $site)
                            <option value="{{ $site->id }}">{{ $site->name }}</option>
                        @endforeach
                    </select>
                    @error('site_id') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="invoice_date" class="block text-sm font-medium text-gray-700 mb-1">Invoice Date</label>
                    <input type="date" name="invoice_date" id="invoice_date" required
                        class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                    @error('invoice_date') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- DESCRIPTION --}}
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description (optional)</label>
                <textarea name="description" id="description" rows="2"
                    class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200"></textarea>
            </div>

            {{-- INVOICE ITEMS --}}
            <div class="bg-white shadow-md rounded-lg border border-gray-200 p-5 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Guard Services</h2>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm border-collapse">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700 uppercase text-xs">
                                <th class="px-4 py-2 text-left">Guard</th>
                                <th class="px-4 py-2 text-left">Days</th>
                                <th class="px-4 py-2 text-left">Rate (Rs)</th>
                                <th class="px-4 py-2 text-left">Subtotal (Rs)</th>
                                <th class="px-4 py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in items" :key="index">
                                <tr class="border-t">
                                    <td class="px-4 py-2">
                                        <select x-model="item.employee_id" :name="`items[${index}][employee_id]`"
                                            class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                                            <option value="">-- Select Guard --</option>
                                            @foreach ($employees as $emp)
                                                <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <td class="px-4 py-2">
                                        <input type="number" min="1" x-model.number="item.days" :name="`items[${index}][days]`"
                                            @input="calculateTotal"
                                            class="w-24 rounded-md border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                                    </td>

                                    <td class="px-4 py-2">
                                        <input type="number" step="0.01" min="0" x-model.number="item.rate" :name="`items[${index}][rate]`"
                                            @input="calculateTotal"
                                            class="w-24 rounded-md border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                                    </td>

                                    <td class="px-4 py-2 text-gray-700 font-medium">
                                        Rs<span x-text="(item.days * item.rate).toFixed(2)"></span>
                                    </td>

                                    <td class="px-4 py-2 text-right">
                                        <button type="button" @click="removeItem(index)" class="text-red-600 hover:text-red-800">
                                            &times;
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 text-right">
                    <button type="button" @click="addItem"
                        class="px-3 py-1.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                        + Add Guard
                    </button>
                </div>
            </div>

            {{-- TOTAL --}}
            <div class="flex justify-end mb-6">
                <div class="text-right">
                    <p class="text-gray-600">Total Amount</p>
                    <h2 class="text-2xl font-semibold text-gray-800">Rs<span x-text="total.toFixed(2)"></span></h2>
                </div>
            </div>

            {{-- HIDDEN TOTAL INPUT --}}
            <input type="hidden" name="total_amount" :value="total">

            {{-- SUBMIT --}}
            <div class="text-right">
                <button type="submit"
                    class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Save Invoice
                </button>
            </div>
        </form>
    </div>

    {{-- Alpine.js Component --}}
    <script>
        function invoiceForm() {
            return {
                items: [{ employee_id: '', days: 1, rate: 0 }],
                total: 0,
                addItem() {
                    this.items.push({ employee_id: '', days: 1, rate: 0 });
                    this.calculateTotal();
                },
                removeItem(index) {
                    this.items.splice(index, 1);
                    this.calculateTotal();
                },
                calculateTotal() {
                    this.total = this.items.reduce((sum, item) => {
                        const subtotal = (item.days * item.rate) || 0;
                        return sum + subtotal;
                    }, 0);
                }
            }
        }
    </script>
</x-app-layout>
