<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { width: 100%; padding: 20px; }
        .header { text-align: center; font-size: 18px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        .total { font-weight: bold; }
    </style>
</head>
<body>

    <div class="container">
        <div class="header">Employee Payslip </div>

        <table>
            <tr>
                <th>Employee</th>
                <td>{{ $employee->fname }} {{ $employee->mname ?? '' }} {{ $employee->sname }}</td>
            </tr>
            <tr>
                <th>Month/Year</th>
                <td>{{ $payroll->group }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>{{ $payroll->status }}</td>
            </tr>
            <tr>
                <th>Gross Salary</th>
                <td>{{ number_format($employee->pivot->salary, 2) }}</td>
            </tr>
            <tr>
                <th>Paye</th>
                <td>{{ number_format($employee->pivot->payee, 2) }}</td>
            </tr>
            <tr>
                <th>Net Salary</th>
                <td>{{ number_format($employee->pivot->net_salary, 2) }}</td>
            </tr>
            <tr>
                <th>Earnings</th>
                <td>{{ $employee->pivot->earning_description }} - {{ number_format($employee->pivot->earning_amount, 2) }}</td>
            </tr>
            <tr>
                <th>Deductions</th>
                <td>{{ $employee->pivot->deduction_description ?? 'N/A' }} - {{ number_format($employee->pivot->deduction_amount, 2) }}</td>
            </tr>
            <tr class="total">
                <th>Total Paid</th>
                <td>{{ number_format($employee->pivot->total_paid, 2) }}</td>
            </tr>
            <tr>
                <th>Paid on</th>
                <td>{{ $payroll_date }}</td>
            </tr>
            <tr>
                <th>Payment Method</th>
                <td>{{ $payment_method }}</td>
            </tr>
        </table>
    </div>

</body>
</html>
