<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            border-bottom: 2px solid #2563eb;
            margin-bottom: 18px;
            padding-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header img {
            height: 50px;
        }
        .header h1 {
            font-size: 20px;
            margin: 0;
            color: #2563eb;
        }
        .invoice-info, .site-info {
            margin-bottom: 15px;
        }
        .invoice-info table, .site-info table {
            width: 100%;
        }
        .invoice-info td, .site-info td {
            padding: 3px 0;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .table th {
            background-color: #f3f4f6;
            color: #111827;
        }
        .total-section {
            text-align: right;
            margin-top: 8px;
        }
        .status {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            text-transform: capitalize;
            font-weight: bold;
            color: white;
        }
        .status.draft { background-color: #9ca3af; }
        .status.sent { background-color: #3b82f6; }
        .status.paid { background-color: #10b981; }
        .status.cancelled { background-color: #ef4444; }

        footer {
            border-top: 1px solid #ddd;
            margin-top: 20px;
            padding-top: 8px;
            text-align: center;
            font-size: 11px;
            color: #6b7280;
        }

        .summary-table {
            width: 50%;
            float: right;
            margin-top: 8px;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 6px;
            border: 1px solid #ddd;
        }

        .summary-table .label {
            background-color: #f3f4f6;
            font-weight: bold;
        }
    </style>
</head>
<body>
    {{-- HEADER --}}
    <div class="header" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
        <!-- Title -->
        <div class="title-block" style="display: flex; flex-direction: column; justify-content: center; height: 5px;">
            <h1 style="margin: 0; color: #2563eb;">Invoice</h1>
            <p style="margin: 0; font-size: 14px;">{{ config('app.name', 'Security System') }}</p>
        </div>
            {{-- Update logo path if available --}}
        <div class="logo-container" style="text-align: right; height: 90px; display: flex; align-items: center;">
            <img src="{{ public_path('images/invoiceImage.jpg') }}" alt="Company Logo"
                style="height: 90px; display: block;" onerror="this.style.display='none'">
            @if (!file_exists(public_path('images/invoiceImage.jpg')))
                <div
                    style="height: 90px; width: 90px; background: #2563eb; color: white; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: bold; border-radius: 10px;">
                    COMPANY<br>LOGO
                </div>
            @endif
        </div>
    </div>

    {{-- INVOICE DETAILS --}}
    <div class="invoice-info">
        <table>
            <tr>
                <td><strong>Invoice No:</strong> {{ $invoice->invoice_number }}</td>
                <td><strong>Date:</strong> {{ $invoice->invoice_date->format('Y-m-d') }}</td>
            </tr>
            <tr>
                <td><strong>Generated On:</strong> {{ now()->format('Y-m-d H:i') }}</td>
                <td><strong>Status:</strong> {{ ucfirst($invoice->status) }}</td>
            </tr>
        </table>
    </div>

    {{-- SITE INFO --}}
    <div class="site-info">
        <table>
            <tr>
                <td><strong>Site:</strong> {{ $invoice->site->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Description:</strong> {{ $invoice->description ?? '—' }}</td>
            </tr>
        </table>
    </div>

    {{-- RANK SERVICES --}}
    @php
        $rankServices = $invoice->items->where('type', 'rank_service');
        $rankTotal = $rankServices->sum('subtotal');
    @endphp
    @if ($rankServices->count() > 0)
        <h3 style="margin-top: 8px; margin-bottom: 10px; color: #2563eb;">Rank Services</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Rank</th>
                <th>No. of Shifts</th>
                <th>Rate (Rs)</th>
                <th>Subtotal (Rs)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rankServices as $item)
                <tr>
                    <td>{{ $item->rank }}</td>
                    <td>{{ $item->number_of_shifts }}</td>
                    <td>Rs{{ number_format($item->rate, 2) }}</td>
                    <td>Rs{{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
                <tr style="font-weight: bold; background-color: #f3f4f6;">
                    <td colspan="3" style="text-align: right;">Rank Services Total:</td>
                    <td>Rs{{ number_format($rankTotal, 2) }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    {{-- OTHER CHARGES --}}
    @php
        $otherCharges = $invoice->items->where('type', 'other_charge');
        $otherTotal = $otherCharges->sum('subtotal');
    @endphp
    @if ($otherCharges->count() > 0)
        <h3 style="margin-top: 8px; margin-bottom: 8px; color: #2563eb;">Other Charges</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Charge Item</th>
                    <th>Description</th>
                    <th>Price (Rs)</th>
                    <th>Subtotal (Rs)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($otherCharges as $charge)
                    <tr>
                        <td>{{ $charge->description ?? $charge->rank }}</td>
                        <td>{{ $charge->description ?? '-' }}</td>
                        <td>Rs{{ number_format($charge->rate, 2) }}</td>
                        <td>Rs{{ number_format($charge->subtotal, 2) }}</td>
                    </tr>
                @endforeach
                <tr style="font-weight: bold; background-color: #f3f4f6;">
                    <td colspan="3" style="text-align: right;">Other Charges Total:</td>
                    <td>Rs{{ number_format($otherTotal, 2) }}</td>
                </tr>
        </tbody>
        </table>
    @endif

    {{-- TOTAL SUMMARY --}}
    <table class="summary-table">
        <tr>
            <td class="label">Rank Services Total:</td>
            <td>Rs{{ number_format($rankTotal, 2) }}</td>
        </tr>
        @if ($otherCharges->count() > 0)
            <tr>
                <td class="label">Other Charges Total:</td>
                <td>Rs{{ number_format($otherTotal, 2) }}</td>
            </tr>
        @endif
        <tr style="font-weight: bold; font-size: 10px;">
            <td class="label">Total Amount:</td>
            <td>Rs{{ number_format($invoice->total_amount, 2) }}</td>
        </tr>
    </table>

    {{-- Clear float --}}
    <div style="clear: both;"></div>

    {{-- Account INFO --}}
    <div class="bank-info" style="margin-top: 8px;">
        <table>
            <tr>
                <td><strong>Bank Name:</strong> Seylan Bank</td>
            </tr>
            <tr>
                <td><strong>Branch:</strong> Nugegoda Branch</td>
            </tr>
             <tr>
                <td><strong>Account Name:</strong> Smart Syndicates (PVT) Ltd</td>
            </tr>
             <tr>
                <td><strong>Account Number:</strong> 0120-13651103-00</td>
            </tr>
            <tr>
                <td><strong>SWIFT Code:</strong> SEYBLKLX</td>
            </tr>
            <tr>
                <td><strong>Payment Terms:</strong> Net 07 Days</td>
            </tr>
        </table>
    </div>
    {{-- THANK YOU MESSAGE --}}
    <div class="thank-you" style="margin-top: 10px; text-align: center;">
        <p>Thank you for choosing <strong>Smart Syndicates Security & Investigations.</strong></p>
        <p>For any queries regarding this invoice, please get in touch with us at
            <strong style="color: #2563eb;">kamanthap@smartsyndicates.lk</strong>
        </p>
    </div>

    {{-- FOOTER --}}
    <div class="footer" style="margin-top: 20px; text-align: center; font-size: 11px; color: #333; line-height: 1.5;">
        <div style="font-weight: bold; font-size: 13px; margin-bottom: 5px;">SMART SYNDICATES</div>
        <div style="margin-bottom: 5px;">SERVICE &nbsp;&nbsp; INTEGRITY &nbsp;&nbsp; RELIABILITY</div>

        <div class="contact-info" style="margin-top: 5px;">
            <div style="color: #2563eb;">www.smartsyndicates.lk</div>
            <div style="color: #2563eb;">info@smartsyndicates.lk &nbsp;&nbsp; | &nbsp;&nbsp; 0113 478 885 &nbsp;&nbsp; |
                &nbsp;&nbsp; 0711 322
                800</div>
            <div>85/1, Elhena Road, Maharagama, Sri Lanka</div>
        </div>
    </div>
</body>
</html>
