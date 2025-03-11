<?php

namespace App\Http\Controllers;


use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use app\Models\Employee;
use App\Models\Payroll;


class PayslipController extends Controller
{
    // public function generatePdf()
    // {
    //     $employee = Employee::get();
    //     $data = [
    //         'title' => 'Payslip ',
    //         'date' => date('d/m/y'),
    //         'employee' => $employee
    //     ];
    //     $pdf = Pdf::loadView('payrolls.includes.payslip', $data);
    //     return $pdf->download('payslip.pdf');
    // }

        public function downloadPayslip($employeeId, $payrollId)
    {
        // Fetch the payroll details for the employee
        $payroll = Payroll::where('id', $payrollId)
            ->with(['employees' => function ($query) use ($employeeId) {
                $query->where('id', $employeeId);
            }])
            ->first();

        if (!$payroll || $payroll->employees->isEmpty()) {
            abort(404, 'Payroll details not found.');
        }

        $employee = $payroll->employees->first();

        // Prepare data for the PDF
        $data = [
            'employee' => $employee,
            'payroll' => $payroll,
            'payroll_date' => $payroll->payroll_date ?? '-',
            'payment_method' => $payroll->payment_method ?? '-',
        ];

        // Load the Blade view into DOMPDF
        $pdf = Pdf::loadView('payrolls.includes.payslip', $data);

        // Return the PDF as a download
        return $pdf->download("Payslip_{$employee->fname}_{$employee->sname}.pdf");
    }
}