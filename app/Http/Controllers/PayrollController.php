<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    /**
     * Fetch all payroll data for API requests.
     */
public function index(Request $request)
{
    // Fetch payrolls grouped by period
    // $payrolls = Payroll::select(
    //     DB::raw('DATE_FORMAT(payroll_date, "%Y-%m") as period'),
    //     DB::raw('SUM(total_amount) as totalNetPay'),
    //     DB::raw('MAX(payroll_date) as date'), // Latest date in the period
    //     DB::raw('COUNT(id) as recordCount'), // Count records per period
    //     DB::raw('MAX(status) as status') // Latest status in the period
    // )
    // ->groupBy('period')
    // ->orderBy('date', 'desc')
    // ->get();

    $payrolls = Payroll::get();

    // Return JSON if requested
    if ($request->wantsJson()) {
        return response()->json($payrolls);
    }

    // Return to Blade view
    return view('payrolls.payroll_summary', compact('payrolls'));
}


// public function show($period)
// {
//     // Decode period (if URL-encoded)
//     $decodedPeriod = urldecode($period);

//     // Fetch payroll records for the selected period
//     $payrolls = Payroll::with('employee') // Load employee details
//         ->select(
//             'employee_id',
//             'gross_pay',
//             'net_pay',
//             'other_deductions',
//             'payment_method',
//             'payment_status',
//             'payment_date',
//             DB::raw('DATE_FORMAT(payment_date, "%Y-%m") as period')
//         )
//         ->whereRaw('DATE_FORMAT(payment_date, "%Y-%m") = ?', [$decodedPeriod])
//         ->orderBy('payment_date', 'desc')
//         ->get();

//     // Compute summary values
//     $totalAmount = $payrolls->sum('net_pay');
//     $recordCount = $payrolls->count();
//     $latestPayroll = $payrolls->first(); // Get latest payroll entry for additional details
//     $monthYear = $decodedPeriod;

//     // Pass to the view
//     return view('payrolls.payroll', compact('payrolls', 'totalAmount', 'recordCount', 'monthYear', 'latestPayroll'));
// }
    public function show(payroll $payroll)
    {
        $payroll = Payroll::with('Client', 'Employees')->find($payroll->id ?? null);

        if (!$payroll) {
            // Handle the case where the payroll is not found
            abort(404, 'Payroll not found');
        }
        return response()->json([$payroll], 200);
    }

    public function viewEmployeePayroll($employee, $payroll, $payslip)
    {
        // Fetch the specific payroll details for a particular employee
        $employeePayroll = Payroll::where('id', $payroll)
            ->whereHas('employees', function ($query) use ($employee) {
                $query->where('id', $employee);
            })
            ->with(['employees' => function ($query) use ($employee) {
                $query->where('id', $employee); // Load details for the specific employee
            }])
            ->first();

        if ($employeePayroll) {
            if ($payslip == '1') {
                // Implement PDF export logic here (using a package like DomPDF)
                $pdf = PDF::loadView('pdf.employeePayroll', ['employeePayroll' => $employeePayroll]) ->setPaper('a4', 'portrait');
                return $pdf->download('Payslip'. ' ' . $employeePayroll->employees[0]->fname . ' ' . $employeePayroll->employees[0]->sname . '.pdf');
            } else {
                return response()->json([$employeePayroll], 200);
            }
        } else {
            return response()->json(['message' => 'No data found for the provided payroll and employee ID.'], 404);
        }
    }


    public function status(Request $request)
    {
        $post = $request->all();

        // Find the payroll record by its ID
        $payrollRecord = Payroll::findOrFail($post['payroll']);

        // Update the status
        $payrollRecord->status = $post['status']; // Adjust this based on your actual field
        $payrollRecord->save();

        // Return a response
        return response()->json([
            'message' => 'Payroll status updated successfully!',
            'status' => $post['status'],
        ]);
    }

    public function exportPayroll($payroll, $type)
    {
        // Fetch payroll records for the specified client
        $payroll = Payroll::with('employees', 'client')->find($payroll);

        // Check for export type and handle accordingly
        if ($type === 'csv') {
            // Define the CSV file path
            $fileName = $payroll->client->client_name . ' payroll' . '.csv';
            $filePath = storage_path('app/public/' . $fileName);

            // Open the file for writing
            $file = fopen($filePath, 'w');

            // Add the header row
            fputcsv($file, [
                'Client Name',
                'Pay period',
                'Client Contact',
                'Client Address',
            ]);

            // Add client information row
            fputcsv($file, [
                $payroll->client->client_name,
                $payroll->group,
                $payroll->client->contact_number ?? 'N/A',
                $payroll->client->address ?? 'N/A',
                '', // Empty cell for spacing
            ]);

            // Add the header row
            fputcsv($file, [
                'Employee No',
                'Employee Name',
                'Gross',
                'PAYE',
                'Net Pay',
                'Earnings',
                'Deductions',
                'Status',
            ]);

            // Add payroll data to the CSV
            foreach ($payroll->employees as $employee) {
                fputcsv($file, [
                    $employee->employee_no,
                    $employee->fname . ' '.$employee->fname .' '. $employee->sname,
                    number_format($employee->pivot->salary, 2), // Formatting salary
                    number_format($employee->pivot->payee ?? 0, 2), // PAYE
                    number_format($employee->pivot->net_salary ?? 0, 2), // Net pay
                    number_format($employee->pivot->earning_amount ?? 0, 2), // Assuming bonus is in the pivot
                    number_format($employee->pivot->deduction_amount ?? 0, 2), // Deductions
                    $payroll->status, // Status of the payroll
                ]);
            }

            // Close the file
            fclose($file);

            // Return the file for download
            return response()->download($filePath)->deleteFileAfterSend(true);
        }
         elseif ($type === 'pdf') {
           // Implement PDF export logic here (using a package like DomPDF)
            $pdf = Pdf::loadView('pdf.payroll', ['payroll' => $payroll])
            ->setPaper('A4', 'landscape'); // Set paper size and orientation

            return $pdf->download($payroll->client->client_name . ' payroll.pdf');
        } else {
            // Handle unsupported export type
            return response()->json(['error' => 'Unsupported export type'], Response::HTTP_BAD_REQUEST);
        }
    }


}
