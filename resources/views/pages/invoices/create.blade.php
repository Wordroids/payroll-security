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
                    <select name="site_id" id="site_id" required x-model="selectedSite" @change="fetchRankRates()"
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
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Rank Services</h2>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm border-collapse">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700 uppercase text-xs">
                                <th class="px-4 py-2 text-left">Rank</th>
                                <th class="px-4 py-2 text-left">Number of Shifts</th>
                                <th class="px-4 py-2 text-left">Rate (Rs)</th>
                                <th class="px-4 py-2 text-left">Subtotal (Rs)</th>
                                <th class="px-4 py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in items" :key="index">
                                <tr class="border-t">
                                    <td class="px-4 py-2">
                                        <select x-model="item.rank" :name="`items[${index}][rank]`"
                                            @change="updateRate(index)"
                                            class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                                            <option value="">-- Select Rank --</option>
                                            <template x-for="rank in availableRanks" :key="rank">
                                                <option :value="rank" x-text="rank"></option>
                                            </template>
                                        </select>
                                    </td>

                                    <td class="px-4 py-2">
                                        <input type="number" min="1" x-model.number="item.number_of_shifts"
                                            :name="`items[${index}][number_of_shifts]`" @input="calculateTotal"
                                            class="w-24 rounded-md border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                                    </td>

                                    <td class="px-4 py-2">
                                        <input type="number" step="0.01" min="0" x-model.number="item.rate"
                                            :name="`items[${index}][rate]`" @input="calculateTotal"
                                            class="w-24 rounded-md border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                                    </td>

                                    <td class="px-4 py-2 text-gray-700 font-medium">
                                        Rs<span x-text="(item.number_of_shifts * item.rate).toFixed(2)"></span>
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
                        + Add Rank
                    </button>
                </div>
            </div>
            {{-- OTHER CHARGES --}}
            <div class="bg-white shadow-md rounded-lg border border-gray-200 p-5 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Other Charges (Optional)</h2>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm border-collapse">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700 uppercase text-xs">
                                <th class="px-4 py-2 text-left">Charge Item</th>
                                <th class="px-4 py-2 text-left">Description (optional)</th>
                                <th class="px-4 py-2 text-left">Price (Rs)</th>
                                <th class="px-4 py-2 text-left">Subtotal (Rs)</th>
                                <th class="px-4 py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(charge, index) in otherCharges" :key="index">
                                <tr class="border-t">
                                    <td class="px-4 py-2">
                                        <input type="text" x-model="charge.item"
                                            :name="`other_charges[${index}][item]`"
                                            class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="text" x-model="charge.description"
                                            :name="`other_charges[${index}][description]`"
                                            class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="number" step="0.01" min="0" x-model.number="charge.price"
                                            :name="`other_charges[${index}][price]`" @input="calculateTotal"
                                            class="w-24 rounded-md border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                                    </td>
                                    <td class="px-4 py-2 text-gray-700 font-medium">
                                        Rs<span x-text="charge.price.toFixed(2)"></span>
                                    </td>
                                    <td class="px-4 py-2 text-right">
                                        <button type="button" @click="removeOtherCharge(index)" class="text-red-600 hover:text-red-800">&times;</button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 text-right">
                    <button type="button" @click="addOtherCharge"
                        class="px-3 py-1.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                        + Add Other Charge
                    </button>
                </div>
            </div>

            {{-- SPECIAL OT --}}
            <div class="bg-white shadow-md rounded-lg border border-gray-200 p-5 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Special OT (Optional)</h2>

                <div class="overflow-x-auto mb-4">
                    <table class="min-w-full text-sm border-collapse">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700 uppercase text-xs">
                                <th class="px-4 py-2 text-left">Rank</th>
                                <th class="px-4 py-2 text-left">Number of OT Hours</th>
                                <th class="px-4 py-2 text-left">Rate per Hour (Rs)</th>
                                <th class="px-4 py-2 text-left">Subtotal (Rs)</th>
                                <th class="px-4 py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(ot, index) in specialOt" :key="index">
                                <tr class="border-t">
                                    <td class="px-4 py-2">
                                        <select x-model="ot.rank"
                                            :name="`special_ot[${index}][rank]`"
                                            @change="updateOtRate(index)"
                                            class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                                            <option value="">-- Select Rank --</option>
                                            <template x-for="rank in availableRanks" :key="rank">
                                                <option :value="rank" x-text="rank"></option>
                                            </template>
                                        </select>
                                    </td>

                                    <td class="px-4 py-2">
                                        <input type="number" min="0" step="0.5" x-model.number="ot.hours"
                                            :name="`special_ot[${index}][hours]`" @input="calculateTotal"
                                            class="w-32 rounded-md border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                                    </td>

                                    <td class="px-4 py-2">
                                        <input type="number" step="0.01" min="0" x-model.number="ot.rate"
                                            :name="`special_ot[${index}][rate]`" @input="calculateTotal"
                                            class="w-32 rounded-md border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                                    </td>

                                    <td class="px-4 py-2 text-gray-700 font-medium">
                                        Rs<span x-text="(ot.hours * ot.rate).toFixed(2)"></span>
                                    </td>

                                    <td class="px-4 py-2 text-right">
                                        <button type="button" @click="removeSpecialOt(index)"
                                            class="text-red-600 hover:text-red-800">
                                            &times;
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot x-show="specialOt.length > 0">
                            <tr class="border-t">
                                <td colspan="3" class="px-4 py-2 text-right font-medium">Special OT Total:</td>
                                <td class="px-4 py-2 text-gray-700 font-medium">
                                    Rs<span x-text="specialOtTotal.toFixed(2)"></span>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="text-right">
                    <button type="button" @click="addSpecialOt"
                        class="px-3 py-1.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                        + Add OT Record
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
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Save Invoice
                </button>
            </div>
        </form>
    </div>

    {{-- Alpine.js Component --}}
    <script>
        function invoiceForm() {
            return {
                selectedSite: '',
                availableRanks: [],
                rankRates: {},
                defaultSpecialOtRate: 0,

                items: [{
                    rank: '',
                    number_of_shifts: 1,
                    rate: 0
                }],

                otherCharges: [],

                specialOt: [],

                total: 0,

                /* ---------------- FETCH RATES ---------------- */
                async fetchRankRates() {
                    if (!this.selectedSite) return;

                    try {
                        const response = await fetch(`/sites/${this.selectedSite}/rank-rates`);
                        const data = await response.json();

                        this.availableRanks = data.ranks || [];
                        this.rankRates = data.rates || {};
                        this.defaultSpecialOtRate = Number(data.special_ot_rate) || 0;

                        // Update rank service rates
                        this.items.forEach((item, index) => {
                            if (item.rank && this.rankRates[item.rank]) {
                                this.items[index].rate = this.rankRates[item.rank];
                            }
                        });

                    } catch (error) {
                        console.error('Error fetching rank rates', error);
                    }
                },

                /* ---------------- RANK SERVICES ---------------- */
                addItem() {
                    this.items.push({
                        rank: '',
                        number_of_shifts: 1,
                        rate: 0
                    });
                },

                removeItem(index) {
                    this.items.splice(index, 1);
                    this.calculateTotal();
                },

                updateRate(index) {
                    const rank = this.items[index].rank;
                    if (rank && this.rankRates[rank]) {
                        this.items[index].rate = this.rankRates[rank];
                    }
                    this.calculateTotal();
                },

                /* ---------------- OTHER CHARGES ---------------- */
                addOtherCharge() {
                    this.otherCharges.push({
                        item: '',
                        description: '',
                        price: 0
                    });
                },

                removeOtherCharge(index) {
                    this.otherCharges.splice(index, 1);
                    this.calculateTotal();
                },

                /* ---------------- SPECIAL OT ---------------- */
                addSpecialOt() {
                    this.specialOt.push({
                        rank: '',
                        hours: 0,
                        rate: this.defaultSpecialOtRate
                    });
                },

                removeSpecialOt(index) {
                    this.specialOt.splice(index, 1);
                    this.calculateTotal();
                },

                updateOtRate(index) {
                    // DO NOT override user-entered value
                    if (!this.specialOt[index].rate) {
                        this.specialOt[index].rate = this.defaultSpecialOtRate;
                    }
                    this.calculateTotal();
                },

                /* ---------------- TOTALS ---------------- */
                calculateTotal() {
                    const rankTotal = this.items.reduce(
                        (sum, item) => sum + (item.number_of_shifts * item.rate || 0), 0
                    );

                    const otherTotal = this.otherCharges.reduce(
                        (sum, charge) => sum + (charge.price || 0), 0
                    );

                    const specialOtTotal = this.specialOt.reduce(
                        (sum, ot) => sum + (ot.hours * ot.rate || 0), 0
                    );

                    this.total = rankTotal + otherTotal + specialOtTotal;
                },

                get specialOtTotal() {
                    return this.specialOt.reduce(
                        (sum, ot) => sum + (ot.hours * ot.rate || 0), 0
                    );
                }
            }
        }
    </script>
</x-app-layout>