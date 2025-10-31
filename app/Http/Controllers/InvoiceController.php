<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Site;
use App\Models\Employee;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('site')->latest()->paginate(10);
        return view('pages.invoices.index', compact('invoices'));
    }

    public function create()
    {
        $sites = Site::with('rankRates')->get();
        return view('pages.invoices.create', compact('sites'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'invoice_date' => 'required|date',
            'description' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.rank' => 'required|string',
            'items.*.number_of_guards' => 'required|integer|min:1',
            'items.*.days' => 'required|integer|min:1',
            'items.*.rate' => 'required|numeric|min:0',
        ]);

        $nextNumber = str_pad(Invoice::count() + 1, 4, '0', STR_PAD_LEFT);
        $validated['invoice_number'] = 'INV/' . date('Y') . '/' . $nextNumber;

        // Calculate totals
        $total = collect($validated['items'])->sum(fn($item) =>
            $item['number_of_guards'] * $item['days'] * $item['rate']
        );
        $validated['total_amount'] = $total;

        $invoice = Invoice::create($validated);

        foreach ($validated['items'] as $item) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'rank' => $item['rank'],
                'number_of_guards' => $item['number_of_guards'],
                'days' => $item['days'],
                'rate' => $item['rate'],
                'subtotal' => $item['number_of_guards'] * $item['days'] * $item['rate'],
            ]);
        }

        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['site', 'items']);
        return view('pages.invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $sites = Site::all();
        $invoice->load('items');
        return view('pages.invoices.edit', compact('invoice', 'sites'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,sent,paid,cancelled',
        ]);

        $invoice->update($validated);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice status updated successfully.');
    }


    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return back()->with('success', 'Invoice deleted.');
    }

    public function downloadPdf(Invoice $invoice)
    {
        $invoice->load(['site', 'items']);
        $pdf = Pdf::loadView('pages.invoices.pdf', compact('invoice'));
        $filename = str_replace(['/', '\\'], '-', $invoice->invoice_number) . '.pdf';
        return $pdf->download($filename);
    }

    // to fetch rank rates for a site

    public function getRankRates(Site $site)
    {
        try {
            // Load the rank rates relationship
            $site->load('rankRates');

            $rankRates = $site->rankRates->pluck('site_shift_rate', 'rank')->toArray();
            $ranks = array_keys($rankRates);

            \Log::info('Rank rates for site ' . $site->id, [
                'ranks' => $ranks,
                'rates' => $rankRates
            ]);

            return response()->json([
                'ranks' => $ranks,
                'rates' => $rankRates
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching rank rates: ' . $e->getMessage());
            return response()->json([
                'ranks' => [],
                'rates' => []
            ], 500);
        }
    }
}
