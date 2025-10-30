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
