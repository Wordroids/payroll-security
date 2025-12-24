<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Site;
use App\Models\Employee;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

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
            'special_ot' => 'nullable|array',
            'special_ot.*.rank' => 'required|string',
            'special_ot.*.hours' => 'required|numeric|min:0',
            'special_ot.*.rate' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($validated) {

            $year = now()->year;

            $lastInvoice = Invoice::where('invoice_number', 'like', "INV/{$year}/%")
                ->orderByDesc('id')
                ->first();

            if ($lastInvoice) {

                $lastNumber = (int) substr($lastInvoice->invoice_number, -4);
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }

            $invoiceNumber = 'INV/' . $year . '/' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

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
            $specialOtTotal = 0;
            if (!empty($validated['special_ot'])) {
                foreach ($validated['special_ot'] as $otRecord) {
                    $subtotal = $otRecord['hours'] * $otRecord['rate'];
                    if ($subtotal > 0) {
                        InvoiceItem::create([
                            'invoice_id' => $invoice->id,
                            'type' => 'special_ot',
                            'rank' => $otRecord['rank'],
                            'special_ot_hours' => $otRecord['hours'],
                            'special_ot_rate' => $otRecord['rate'],
                            'number_of_shifts' => $otRecord['hours'],
                            'rate' => $otRecord['rate'],
                            'subtotal' => $subtotal,
                            'description' => 'Special OT - ' . $otRecord['rank'],
                        ]);
                        $specialOtTotal += $subtotal;
                    }
                }
            }

            // Update total
            $invoice->update([
                'total_amount' => $rankTotal + $otherTotal + $specialOtTotal,
            ]);

            return redirect()->route('invoices.index')->with('success', 'Invoice created successfully.');
        });
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
