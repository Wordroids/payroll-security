<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CFormRecord;
use App\Models\Employee;

class CFormController extends Controller
{

    // index page
    public function index(Request $request)
    {
        $month = $request->get('month', date('F'));
        $year = $request->get('year', date('Y'));
        $records = CFormRecord::with('employee')->where('month', $month)->where('year', $year)->get();

        $totals = [
            'total_employer_epf' => $records->sum('employer_epf'),
            'total_employee_epf' => $records->sum('employee_epf'),
            'total_combined' => $records->sum('employer_epf') + $records->sum('employee_epf'),
        ];
        return view('pages.epf.form-c.index', compact('records', 'totals', 'month', 'year'));
    }

    //create form C record
    public function create()
    {
        $employees = Employee::orderBy('emp_no')->get();
        return view('pages.epf.form-c.create', compact('employees'));
    }

    public function store(Request $request)
    {
        try {
            $earnings = $request->total_earnings;

            $data = [
                'employee_id'    => $request->employee_id,
                'member_no'      => $request->member_no,
                'month'          => $request->month,
                'year'           => $request->year,
                'total_earnings' => $earnings,
                'employer_epf'   => $earnings * 0.12,
                'employee_epf'   => $earnings * 0.08,
                'surcharge'      => $request->surcharge ?? 0,
                'etf_contribution' => $earnings * 0.03,

            ];

            CFormRecord::create($data);

            return redirect()->route('epf.form-c.index')->with('success', 'Form C Record added.');
        } catch (\Exception $e) {

            dd($e->getMessage());
        }
    }

    //edit form C record
    public function edit($id)
    {
        $record = CFormRecord::findOrFail($id);
        $employees = Employee::orderBy('emp_no')->get();
        return view('pages.epf.form-c.edit', compact('record', 'employees'));
    }

    public function update(Request $request, $id)
    {
        $record = CFormRecord::findOrFail($id);
        $earnings = $request->total_earnings;
        $record->update([
            'employee_id' => $request->employee_id,
            'member_no' => $request->member_no,
            'month' => $request->month,
            'year' => $request->year,
            'total_earnings' => $earnings,
            'employer_epf' => $earnings * 0.12,
            'employee_epf' => $earnings * 0.08,
            'surcharge' => $request->surcharge ?? 0,
            'etf_contribution' => $earnings * 0.03,

        ]);
        return redirect()->route('epf.form-c.index', ['month' => $record->month, 'year' => $record->year]);
    }

    //delete form C record
    public function destroyEtf($id)
    {
        $record = CFormRecord::findOrFail($id);
        $month = $record->month;
        $year = $record->year;

        $record->delete();

        return redirect()->route('epf.form-r.index', ['month' => $month, 'year' => $year])
            ->with('success', 'ETF Record deleted successfully.');
    }

    //bank details
    public function bankDetails()
    {
        $records = CFormRecord::select('month', 'year', 'bank_name', 'branch_name', 'cheque_no')
            ->whereNotNull('bank_name')->distinct()->orderBy('year', 'desc')->get();
        return view('pages.epf.form-c.bankDetails', compact('records'));
    }

    //create bank details
    public function createBankDetails(Request $request)
    {
        $month = $request->query('month', date('F'));
        $year = $request->query('year', date('Y'));


        return view('pages.epf.form-c.createBankDetails', compact('month', 'year'))->with('record', null);
    }
    public function storeBankDetails(Request $request)
    {
        CFormRecord::where('month', $request->month)->where('year', $request->year)->update([
            'bank_name' => $request->bank_name,
            'branch_name' => $request->branch_name,
            'cheque_no' => $request->cheque_no,
            'cheque_return_charges' => $request->cheque_return_charges ?? 0,
        ]);
        return redirect()->route('epf.form-c.bankDetails');
    }

    //edit bank details
    public function editBankDetails($month, $year)
    {
        $record = CFormRecord::where('month', $month)
            ->where('year', $year)
            ->whereNotNull('bank_name')
            ->first();

        return view('pages.epf.form-c.createBankDetails', compact('month', 'year', 'record'));
    }
    public function destroyBankDetails($month, $year)
    {
        CFormRecord::where('month', $month)
            ->where('year', $year)
            ->update([
                'bank_name'   => null,
                'branch_name' => null,
                'cheque_no'   => null,
                'cheque_return_charges' => 0.00,

            ]);

        return redirect()->route('epf.form-c.bankDetails')
            ->with('success', "Bank details for {$month} {$year} deleted successfully.");
    }

    //print pdf
    public function printPdf($month, $year)
    {

        $records = CFormRecord::with('employee')
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        if ($records->isEmpty()) {
            return redirect()->back()->with('error', 'No records found to print.');
        }

        $firstRecord = $records->first();


        $mpdf = new \Mpdf\Mpdf([
            'format' => 'A4',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
        ]);


        $templatePath = storage_path('app/templates/Form_C_Template.pdf');
        $mpdf->setSourceFile($templatePath);
        $importPage = $mpdf->importPage(1);
        $mpdf->useTemplate($importPage, 0, 0, 210, 297);

        $mpdf->SetFont('helvetica', '', 9);


        $mpdf->Text(15, 30, 'Smart Syndicates (PVT) Ltd,');
        $mpdf->Text(15, 35, 'No 85/1, Elhena Road,');
        $mpdf->Text(15, 40, 'Maharagama');


        $mpdf->Text(155, 23, '53580/B');
        $mpdf->Text(165, 29, $month . ' ' . $year);

        $totalEmployer = $records->sum('employer_epf');
        $totalEmployee = $records->sum('employee_epf');
        $totalContribution = $totalEmployer + $totalEmployee;
        $totalSurcharge = $records->sum('surcharge') ?? 0;
        $totalRemittance = $totalContribution + $totalSurcharge;

        $mpdf->Text(165, 36, number_format($totalContribution, 2));
        $mpdf->Text(165, 43, number_format($totalSurcharge, 2));
        $mpdf->Text(165, 49, number_format($totalRemittance, 2));

        // bank details
        $mpdf->Text(130, 56, $firstRecord->cheque_no ?? '');
        $mpdf->Text(130, 64, ($firstRecord->bank_name ?? '') . ' - ' . ($firstRecord->branch_name ?? ''));


        $startY = 88;
        $rowHeight = 7.5;

        foreach ($records as $index => $record) {
            if ($index >= 15) break;

            $currentY = $startY + ($index * $rowHeight);

            $mpdf->Text(15, $currentY, $record->employee->name);
            $mpdf->Text(75, $currentY, $record->employee->nic);
            $mpdf->Text(100, $currentY, $record->member_no);
            $mpdf->Text(122, $currentY, number_format($record->employer_epf + $record->employee_epf, 2));
            $mpdf->Text(142, $currentY, number_format($record->employer_epf, 2));
            $mpdf->Text(162, $currentY, number_format($record->employee_epf, 2));
            $mpdf->Text(182, $currentY, number_format($record->total_earnings, 2));
        }

        // table footer totals
        $mpdf->Text(122, 243, number_format($totalContribution, 2));
        $mpdf->Text(142, 243, number_format($totalEmployer, 2));
        $mpdf->Text(162, 243, number_format($totalEmployee, 2));

        return response($mpdf->Output('', 'S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "inline; filename=\"Form_C_{$month}_{$year}.pdf\"");
    }
}
