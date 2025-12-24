<x-app-layout>
    <div class="p-6 max-w-3xl mx-auto">
        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-xl font-semibold text-gray-800">
                    Edit Invoice Status
                </h1>
                <p class="text-sm text-gray-500">
                    {{ $invoice->invoice_number }} • {{ $invoice->site->name ?? 'N/A' }}
                </p>
            </div>

            <a href="{{ route('invoices.show', $invoice) }}"
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm">
                ← Back
            </a>
        </div>

        {{-- Invoice Summary --}}
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-sm font-semibold text-gray-600 mb-1">Invoice Number</h2>
                    <p class="text-gray-800">{{ $invoice->invoice_number }}</p>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-600 mb-1">Date</h2>
                    <p class="text-gray-800">{{ $invoice->invoice_date->format('Y-m-d') }}</p>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-600 mb-1">Total Amount</h2>
                    <p class="text-gray-800 font-semibold">Rs{{ number_format($invoice->total_amount, 2) }}</p>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-600 mb-1">Current Status</h2>
                    @php
                    $colors = [
                    'draft' => 'bg-gray-300 text-gray-800',
                    'sent' => 'bg-blue-100 text-blue-700',
                    'paid' => 'bg-green-100 text-green-700',
                    'cancelled' => 'bg-red-100 text-red-700',
                    ];
                    @endphp
                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $colors[$invoice->status] ?? 'bg-gray-200 text-gray-700' }}">
                        {{ ucfirst($invoice->status) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Rank Services Section --}}
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Rank Services</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700 uppercase text-xs">
                            <th class="px-4 py-2 text-left">Rank</th>
                            <th class="px-4 py-2 text-left">Number of Shifts</th>
                            <th class="px-4 py-2 text-left">Rate (Rs)</th>
                            <th class="px-4 py-2 text-left">Subtotal (Rs)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $rankServices = $invoice->items->where('type', 'rank_service');
                        $rankTotal = $rankServices->sum('subtotal');
                        @endphp
                        @forelse ($rankServices as $item)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $item->rank }}</td>
                            <td class="px-4 py-2">{{ $item->number_of_shifts }}</td>
                            <td class="px-4 py-2">Rs{{ number_format($item->rate, 2) }}</td>
                            <td class="px-4 py-2 font-semibold">Rs{{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-gray-500">No rank services added.</td>
                        </tr>
                        @endforelse
                        @if($rankServices->count() > 0)
                        <tr class="border-t font-semibold">
                            <td colspan="3" class="px-4 py-2 text-right">Rank Services Total:</td>
                            <td class="px-4 py-2">Rs{{ number_format($rankTotal, 2) }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Other Charges Section --}}
        @php
        $otherCharges = $invoice->items->where('type', 'other_charge');
        $otherTotal = $otherCharges->sum('subtotal');
        @endphp
        @if($otherCharges->count() > 0)
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Other Charges</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700 uppercase text-xs">
                            <th class="px-4 py-2 text-left">Charge Item</th>
                            <th class="px-4 py-2 text-left">Description</th>
                            <th class="px-4 py-2 text-left">Price (Rs)</th>
                            <th class="px-4 py-2 text-left">Subtotal (Rs)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($otherCharges as $charge)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $charge->description ?? $charge->rank }}</td>
                            <td class="px-4 py-2">{{ $charge->description ?? '-' }}</td>
                            <td class="px-4 py-2">Rs{{ number_format($charge->rate, 2) }}</td>
                            <td class="px-4 py-2 font-semibold">Rs{{ number_format($charge->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                        <tr class="border-t font-semibold">
                            <td colspan="3" class="px-4 py-2 text-right">Other Charges Total:</td>
                            <td class="px-4 py-2">Rs{{ number_format($otherTotal, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        @endif
        {{-- Special OT  --}}
        @php
        $specialOtItems = $invoice->items->where('type', 'special_ot');
        $specialOtTotal = $specialOtItems->sum('subtotal');
        @endphp

        @if($specialOtItems->count() > 0)
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Special OT</h2>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700 uppercase text-xs">
                            <th class="px-4 py-2 text-left">Rank</th>
                            <th class="px-4 py-2 text-left">OT Hours</th>
                            <th class="px-4 py-2 text-left">Rate / Hour (Rs)</th>
                            <th class="px-4 py-2 text-left">Subtotal (Rs)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($specialOtItems as $ot)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $ot->rank }}</td>
                            <td class="px-4 py-2">{{ $ot->special_ot_hours }}</td>
                            <td class="px-4 py-2">Rs{{ number_format($ot->special_ot_rate, 2) }}</td>
                            <td class="px-4 py-2 font-semibold">Rs{{ number_format($ot->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                        <tr class="border-t font-semibold">
                            <td colspan="3" class="px-4 py-2 text-right">Special OT Total:</td>
                            <td class="px-4 py-2">Rs{{ number_format($specialOtTotal, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Status Update Form --}}
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <form method="POST" action="{{ route('invoices.update', $invoice) }}">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Update Status
                    </label>
                    <select name="status" id="status" required
                        class="w-full md:w-1/2 rounded-md border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                        <option value="draft" {{ $invoice->status === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="sent" {{ $invoice->status === 'sent' ? 'selected' : '' }}>Sent</option>
                        <option value="paid" {{ $invoice->status === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="cancelled" {{ $invoice->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="text-right">
                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
