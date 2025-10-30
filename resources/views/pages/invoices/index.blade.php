<x-app-layout>
    <div class="p-6">
        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-xl font-semibold text-gray-800">Invoices</h1>
            <a href="{{ route('invoices.create') }}"
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                + New Invoice
            </a>
        </div>

        {{-- Table --}}
        <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-200">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 border-b text-gray-700 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3">Invoice #</th>
                        <th class="px-6 py-3">Site</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Total (£)</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($invoices as $invoice)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-6 py-3 font-medium text-gray-800">{{ $invoice->invoice_number }}</td>
                            <td class="px-6 py-3">{{ $invoice->site->name ?? 'N/A' }}</td>
                            <td class="px-6 py-3">{{ $invoice->invoice_date->format('Y-m-d') }}</td>
                            <td class="px-6 py-3">£{{ number_format($invoice->total_amount, 2) }}</td>
                            <td class="px-6 py-3">
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
                            </td>
                            <td class="px-6 py-3 text-right space-x-2">
                                <a href="{{ route('invoices.show', $invoice) }}"
                                   class="text-blue-600 hover:underline text-sm">View</a>

                                <a href="{{ route('invoices.edit', $invoice) }}"
                                   class="text-yellow-600 hover:underline text-sm">Edit</a>

                                <a href="{{ route('invoices.download', $invoice) }}"
                                   class="text-green-600 hover:underline text-sm">Download</a>

                                <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('Are you sure you want to delete this invoice?')"
                                            class="text-red-600 hover:underline text-sm">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">No invoices found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $invoices->links() }}
        </div>
    </div>
</x-app-layout>
