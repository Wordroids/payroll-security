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
        $sites = Site::all();
        $employees = Employee::all();
        return view('pages.invoices.create', compact('sites', 'employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'invoice_date' => 'required|date',
            'description' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.employee_id' => 'required|exists:employees,id',
            'items.*.days' => 'required|integer|min:1',
            'items.*.rate' => 'required|numeric|min:0',
        ]);

        $nextNumber = str_pad(Invoice::count() + 1, 4, '0', STR_PAD_LEFT);
        $validated['invoice_number'] = 'INV/' . date('Y') . '/' . $nextNumber;

        // Calculate totals
        $total = collect($validated['items'])->sum(fn($item) => $item['days'] * $item['rate']);
        $validated['total_amount'] = $total;

        $invoice = Invoice::create($validated);

        foreach ($validated['items'] as $item) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'employee_id' => $item['employee_id'],
                'days' => $item['days'],
                'rate' => $item['rate'],
                'subtotal' => $item['days'] * $item['rate'],
            ]);
        }

        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['site', 'items.employee']);
        return view('pages.invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $sites = Site::all();
        $employees = Employee::all();
        $invoice->load('items');
        return view('pages.invoices.edit', compact('invoice', 'sites', 'employees'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,sent,paid,cancelled',
        ]);

        $invoice->update($validated);

        return back()->with('success', 'Invoice status updated.');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return back()->with('success', 'Invoice deleted.');
    }

    public function downloadPdf(Invoice $invoice)
    {
        $invoice->load(['site', 'items.employee']);
        $pdf = Pdf::loadView('pages.invoices.pdf', compact('invoice'));
        $filename = str_replace(['/', '\\'], '-', $invoice->invoice_number) . '.pdf';
        return $pdf->download($filename);
    }
}
