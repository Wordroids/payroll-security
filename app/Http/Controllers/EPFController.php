<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EtfRecord;
use App\Models\Employee;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as MPDF;

class EPFController extends Controller
{
    public function formR(Request $request)
    {
        $month = $request->get('month', date('F'));
        $year = $request->get('year', date('Y'));

        $records = EtfRecord::with('employee')
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        // Summary Totals for the top
        $totals = [
            'total_contribution' => $records->sum('etf_contribution'),
            'total_employer_epf' => $records->sum('employer_epf'),
            'total_employee_epf' => $records->sum('employee_epf'),
        ];

        return view('pages.epf.form-r.index', compact('records', 'totals', 'month', 'year'));
    }

    public function bankDetails(Request $request)
    {
        // Fetch all distinct bank details grouped by month and year
        $records = EtfRecord::select('month', 'year', 'bank_name', 'branch_name', 'cheque_no')
            ->whereNotNull('bank_name')
            ->distinct()
            ->orderBy('year', 'desc')
            ->get();

        return view('pages.epf.form-r.bankDetails', compact('records'));
    }

    public function create()
    {
        $employees = Employee::orderBy('emp_no')->get();
        return view('pages.epf.form-r.create', compact('employees'));
    }

    public function storeEtf(Request $request)
    {
        $earnings = $request->total_earnings;
        $surcharge = $request->surcharge ?? 0;

        EtfRecord::create([
            'employee_id'      => $request->employee_id,
            'month'            => $request->month,
            'year'             => $request->year,
            'member_no'        => $request->member_no,
            'total_earnings'   => $earnings,
            'etf_contribution' => $earnings * 0.03,
            'employer_epf'     => $earnings * 0.12,
            'employee_epf'     => $earnings * 0.08,
            'surcharge'        => $surcharge,
        ]);

        return redirect()->route('epf.form-r.index')
            ->with('success', 'ETF Record added successfully.');
    }

    //edit  record
    public function editEtf($id)
    {
        $record = EtfRecord::findOrFail($id);
        $employees = Employee::orderBy('emp_no')->get();
        return view('pages.epf.form-r.edit', compact('record', 'employees'));
    }

    public function updateEtf(Request $request, $id)
    {
        $record = EtfRecord::findOrFail($id);

        $earnings = $request->total_earnings;
        $surcharge = $request->surcharge ?? 0;

        $record->update([
            'employee_id'      => $request->employee_id,
            'month'            => $request->month,
            'year'             => $request->year,
            'member_no'        => $request->member_no,
            'total_earnings'   => $earnings,
            'etf_contribution' => $earnings * 0.03,
            'employer_epf'     => $earnings * 0.12,
            'employee_epf'     => $earnings * 0.08,
            'surcharge'        => $surcharge,
        ]);

        return redirect()->route('epf.form-r.index', ['month' => $record->month, 'year' => $record->year])
            ->with('success', 'ETF Record updated successfully.');
    }

    //delete record
    public function destroyEtf($id)
    {
        $record = EtfRecord::findOrFail($id);
        $month = $record->month;
        $year = $record->year;

        $record->delete();

        return redirect()->route('epf.form-r.index', ['month' => $month, 'year' => $year])
            ->with('success', 'ETF Record deleted successfully.');
    }

    // bank details
    public function createBankDetails(Request $request)
    {

        $month = $request->query('month', date('F'));
        $year = $request->query('year', date('Y'));
        $record = null;

        return view('pages.epf.form-r.createBankDetails', compact('month', 'year', 'record'));
    }

    public function editBankDetails($month, $year)
    {
        $record = EtfRecord::where('month', $month)
            ->where('year', $year)
            ->whereNotNull('bank_name')
            ->first();

        return view('pages.epf.form-r.createBankDetails', compact('month', 'year', 'record'));
    }

    public function storeBankDetails(Request $request)
    {
        $month = $request->month;
        $year = $request->year;

        $updatedCount = EtfRecord::where('month', $month)
            ->where('year', $year)
            ->update([
                'bank_name'             => $request->bank_name,
                'branch_name'           => $request->branch_name,
                'cheque_no'             => $request->cheque_no,
                'cheque_return_charges' => $request->cheque_return_charges ?? 0.00,
            ]);

        // to prevents users from adding bank details to a month that has NO employees logged yet
        if ($updatedCount === 0) {
            return redirect()->back()->with('error', "No ETF records found for {$month} {$year}. Please add employee records first.");
        }

        return redirect()->route('epf.form-r.bankDetails', ['month' => $month, 'year' => $year])
            ->with('success', 'Bank details updated successfully for all records.');
    }

    //to print pdf
    public function printFormR($month, $year)
    {
        $records = EtfRecord::with('employee')
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        $mpdf = new \Mpdf\Mpdf([
            'format' => 'A4',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
        ]);

        $templatePath = storage_path('app/templates/R4_Remittance_Form_.pdf');

        if (!file_exists($templatePath)) {
            abort(404, "PDF Template not found at {$templatePath}");
        }

        $pageCount = $mpdf->setSourceFile($templatePath);
        $templateId = $mpdf->importPage(1);
        $mpdf->UseTemplate($templateId, 0, 0, 210, 297);

        $mpdf->SetFont('helvetica', '', 10);

        $mpdf->Text(45, 25, 'Smart Syndicates (PVT) Ltd');
        $mpdf->Text(45, 30, 'No 85/1, Elhena Road,');
        $mpdf->Text(45, 35, 'Maharagama');

        $mpdf->Text(128, 24, 'EMP-12345');
        $mpdf->Text(152, 24, $month . ' ' . $year);
        $mpdf->Text(178, 24, (string) $records->count());

        $firstRecord = $records->first();
        $chequeReturnCharges = $firstRecord ? ($firstRecord->cheque_return_charges ?? 0) : 0;

        $totalContribution = $records->sum('etf_contribution');
        $totalSurcharge = $records->sum('surcharge') ?? 0;

        $grandTotal = $totalContribution + $totalSurcharge + $chequeReturnCharges;

        $mpdf->Text(172, 42, number_format($totalContribution, 2));
        $mpdf->Text(172, 50, number_format($totalSurcharge, 2));
        $mpdf->Text(172, 56, number_format($chequeReturnCharges, 2));
        $mpdf->Text(172, 63, number_format($grandTotal, 2));

        if ($firstRecord) {
            $mpdf->Text(172, 69, $firstRecord->cheque_no ?? '');
            $mpdf->Text(170, 75, ($firstRecord->bank_name ?? '') . ' / ');
            $mpdf->Text(170, 78, ($firstRecord->branch_name ?? ''));
        }

        // employee table
        $startY = 97;
        $rowHeight = 8.5;

        $tableRupeesX = 162;
        $tableCentsX = 183;

        foreach ($records as $index => $record) {
            if ($index >= 15) break;

            $currentY = $startY + ($index * $rowHeight);

            $mpdf->Text(35, $currentY, $record->employee->name ?? '');
            $mpdf->Text(105, $currentY, $record->employee->nic ?? '');
            $mpdf->Text(136, $currentY, $record->member_no ?? '');

            $empContribParts = explode('.', number_format($record->etf_contribution, 2, '.', ','));

            $mpdf->Text($tableRupeesX, $currentY, $empContribParts[0]);
            $mpdf->Text($tableCentsX, $currentY, $empContribParts[1]);
        }

        $bottomTotalParts = explode('.', number_format($totalContribution, 2, '.', ','));
        $mpdf->Text($tableRupeesX, 248, $bottomTotalParts[0]);
        $mpdf->Text($tableCentsX, 248, $bottomTotalParts[1]);

        if ($pageCount > 1) {
            $mpdf->AddPage();
            $templateId2 = $mpdf->importPage(2);
            $mpdf->UseTemplate($templateId2, 0, 0, 210, 297);
        }

        return response($mpdf->Output('', 'S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "inline; filename=\"Form_R4_{$month}_{$year}.pdf\"");
    }

    public function destroyBankDetails($month, $year)
    {
        EtfRecord::where('month', $month)
            ->where('year', $year)
            ->update([
                'bank_name'   => null,
                'branch_name' => null,
                'cheque_no'   => null,
                'cheque_return_charges' => 0.00,
            ]);

        return redirect()->route('epf.form-r.bankDetails')
            ->with('success', "Bank details for {$month} {$year} deleted successfully.");
    }
}
