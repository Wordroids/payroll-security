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
            'special_ot.hours' => 'nullable|numeric|min:0',
            'special_ot.rate' => 'nullable|numeric|min:0',

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
        $specialOtHours = $validated['special_ot']['hours'] ?? 0;
        $specialOtRate = $validated['special_ot']['rate'] ?? 0;
        $specialOtSubtotal = $specialOtHours * $specialOtRate;


        if ($specialOtSubtotal > 0) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'type' => 'special_ot',
                'rank' => 'N/A',
                'special_ot_hours' => $specialOtHours,
                'special_ot_rate' => $specialOtRate,
                'number_of_shifts' => $specialOtHours,
                'rate' => $specialOtRate,
                'subtotal' => $specialOtSubtotal,
                'description' => 'Special OT',
            ]);
        }

        // Update total
        $invoice->update([
            'total_amount' => $rankTotal + $otherTotal + $specialOtSubtotal,
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

            // Load OT rate from SalarySetting
            $settings = \App\Models\SalarySetting::getSettings();
            $specialOtRate = $settings->special_ot_rate ?? 0;

            return response()->json([
                'ranks' => array_keys($rankRates),
                'rates' => $rankRates,
                'special_ot_rate' => $specialOtRate
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'ranks' => [],
                'rates' => [],
                'special_ot_rate' => 0
            ], 500);
        }
    }
}
