<!DOCTYPE html>
<html>
<head>
    @include('pdf.css')
</head>
<body>
<div >
    <div class="row">
        <div>
            <div >
                <div class="payslip-header">
    <h3 class="title">Payslip</h3>
    <p><strong>Period:</strong> {{ $employeePayroll->group ?? 'N/A' }}</p>
</div>

<div class="employee-details">
    <p><strong>Employee Name:</strong>
        {{ $employeePayroll->employees[0]->fname }}
        {{ $employeePayroll->employees[0]->mname }}
        {{ $employeePayroll->employees[0]->sname }}
    </p>
    <p><strong>Employee Number:</strong> EMP-{{ $employeePayroll->employees[0]->employee_no }}</p>
    <p><strong>Position:</strong> {{ $employeePayroll->employees[0]->position }}</p>
</div>

                {{-- <table class="table" >
                    <tr>
                        <td>


                        </td>
                         <td class="amount">
                            <strong>Company/Organisation</strong><br>
                            {{ $employeePayroll->client->client_name ?? 'N/A' }}<br>
                            {{ $employeePayroll->client->street_address ?? 'N/A' }}<br>
                            {{ $employeePayroll->client->city ?? 'N/A' }}<br>
                            {{ $employeePayroll->client->country_id ?? 'N/A' }}<br>
                            {{ $employeePayroll->client->phone ?? 'N/A' }}<br>
                            {{ $employeePayroll->client->user->email ?? 'N/A' }}
                        </td>
                    </tr>
                </table>
            </div> --}}
            <table class="payslip-table">
    <thead>
        <tr>
            <th class="text-center">Item</th>
            <th class="text-center">Amount (K)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Gross Pay</td>
            <td class="amount">{{ number_format($employeePayroll->employees[0]->pivot->salary, 2) }}</td>
        </tr>
        <tr>
            <td>Earnings</td>
            <td class="amount">{{ number_format($employeePayroll->employees[0]->pivot->earning_amount, 2) }}</td>
        </tr>
        <tr>
            <td>PAYE</td>
            <td class="amount">{{ number_format($employeePayroll->employees[0]->pivot->payee, 2) }}</td>
        </tr>
        <tr>
            <td>Deductions</td>
            <td class="amount">{{ number_format($employeePayroll->employees[0]->pivot->deductions_amount, 2) }}</td>
        </tr>
        <tr>
            <td>Net Pay</td>
            <td class="amount">{{ number_format($employeePayroll->employees[0]->pivot->net_salary, 2) }}</td>
        </tr>
        <tr class="total-row">
            <td><strong>Total Paid</strong></td>
            <td class="amount"><strong>{{ number_format($employeePayroll->employees[0]->pivot->total_paid, 2) }}</strong></td>
        </tr>
    </tbody>
</table>

        </div>
    </div>
</div>

</div>
<footer class="payslip-footer">
    <p>
        If you need further assistance, please contact
        <strong>HR</strong> at <a href="mailto:hr@testemail.com">hr@testemail.com</a>
    </p>
</footer>

</body>
</html>
