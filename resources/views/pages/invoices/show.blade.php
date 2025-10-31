<x-app-layout>
    <div class="p-6">
        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-xl font-semibold text-gray-800">
                    Invoice Details
                </h1>
                <p class="text-sm text-gray-500">
                    {{ $invoice->invoice_number }} • Created on {{ $invoice->invoice_date->format('Y-m-d') }}
                </p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('invoices.download', $invoice) }}"
                   class="px-4 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">
                    Download PDF
                </a>

                <a href="{{ route('invoices.edit', $invoice) }}"
                   class="px-4 py-2 bg-yellow-500 text-white text-sm rounded-md hover:bg-yellow-600">
                    Edit Status
                </a>

                <a href="{{ route('invoices.index') }}"
                   class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded-md hover:bg-gray-300">
                    Back
                </a>
            </div>
        </div>

        {{-- Invoice Info --}}
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
                    <h2 class="text-sm font-semibold text-gray-600 mb-1">Site</h2>
                    <p class="text-gray-800">{{ $invoice->site->name ?? 'N/A' }}</p>
                </div>

                <div>
                    <h2 class="text-sm font-semibold text-gray-600 mb-1">Status</h2>
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

            @if ($invoice->description)
                <div class="mt-6">
                    <h2 class="text-sm font-semibold text-gray-600 mb-1">Description</h2>
                    <p class="text-gray-700">{{ $invoice->description }}</p>
                </div>
            @endif
        </div>

        {{-- Ranks Table --}}
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Rank Services</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700 uppercase text-xs">
                            <th class="px-4 py-2 text-left">Rank</th>
                            <th class="px-4 py-2 text-left">Number of Guards</th>
                            <th class="px-4 py-2 text-left">Days</th>
                            <th class="px-4 py-2 text-left">Rate (Rs)</th>
                            <th class="px-4 py-2 text-left">Subtotal (Rs)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($invoice->items as $item)
                            <tr class="border-t">
                                <td class="px-4 py-2">{{ $item->rank }}</td>
                                <td class="px-4 py-2">{{ $item->number_of_guards }}</td>
                                <td class="px-4 py-2">{{ $item->days }}</td>
                                <td class="px-4 py-2">Rs{{ number_format($item->rate, 2) }}</td>
                                <td class="px-4 py-2 font-semibold">Rs{{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-gray-500">No rank records added.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Totals --}}
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <div class="flex justify-end">
                <div class="text-right">
                    <p class="text-gray-600 text-sm">Total Amount</p>
                    <h2 class="text-2xl font-bold text-gray-800">Rs{{ number_format($invoice->total_amount, 2) }}</h2>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

