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
            'items.*.number_of_shifts' => 'required|integer|min:1',
            'items.*.rate' => 'required|numeric|min:0',
            'other_charges' => 'nullable|array',
            'other_charges.*.item' => 'required|string',
            'other_charges.*.description' => 'nullable|string',
            'other_charges.*.price' => 'required|numeric|min:0',
        ]);

        $nextNumber = str_pad(Invoice::count() + 1, 4, '0', STR_PAD_LEFT);
        $invoiceNumber = 'INV/' . date('Y') . '/' . $nextNumber;

        $invoice = Invoice::create([
            'invoice_number' => $invoiceNumber,
            'site_id' => $validated['site_id'],
            'invoice_date' => $validated['invoice_date'],
            'description' => $validated['description'] ?? null,
            'total_amount' => 0,
        ]);

        $rankTotal = 0;
        foreach ($validated['items'] as $item) {
            $subtotal = $item['number_of_shifts'] * $item['rate'];
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'rank' => $item['rank'],
                'number_of_shifts' => $item['number_of_shifts'],
                'rate' => $item['rate'],
                'subtotal' => $subtotal,
                'type' => 'rank_service',
            ]);
            $rankTotal += $subtotal;
        }

        $otherTotal = 0;
        if (!empty($validated['other_charges'])) {
            foreach ($validated['other_charges'] as $charge) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'rank' => 'N/A',
                    'number_of_shifts' => 1, 
                    'rate' => $charge['price'],
                    'subtotal' => $charge['price'],
                    'description' => $charge['description'] ?? $charge['item'],
                    'type' => 'other_charge',
                ]);
                $otherTotal += $charge['price'];
            }
        }

        // Update total
        $invoice->update([
            'total_amount' => $rankTotal + $otherTotal,
        ]);

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
