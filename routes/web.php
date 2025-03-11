<?php

use App\Livewire\Clients\AddClient;
use App\Livewire\Clients\UpdateClient;
use App\Livewire\AddTaxRate;
use App\Livewire\AddUser;
use App\Livewire\Attendances;
use App\Livewire\Notifications;
use App\Livewire\Payroll;
use App\Livewire\Reports;
use App\Livewire\Settings;
use App\Livewire\TaxRateList;
use App\Livewire\UserList;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ClientController;
use App\Livewire\Employees\AddEmployee;
use App\Livewire\Employees\UpdateEmployee;
use App\Livewire\Employees\ViewEmployee;

use App\Http\Controllers\BillingController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\SettingsController;

use App\Http\Controllers\PDFController;
use App\Http\Controllers\PayslipController;


Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware(['auth']);
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware(['auth']);

Route::get('/employees', [EmployeeController::class, 'index'])->name('employees')->middleware(['auth']);

//payroll routes
Route::get('/payrolls', [PayrollController::class, 'index'])->name('payrollsummary')->middleware(['auth']);
Route::get('/payrolls/{period}', [PayrollController::class, 'show'])->name('payrolls.show')->middleware(['auth']);


Route::middleware(['auth'])->group(function () {
    Route::get('/billings', [BillingController::class, 'index'])->name('billing');
    Route::get('/billings/view/{id}', [BillingController::class, 'show'])->name('billingView');
    Route::get('/billings/invoice/download{id}', [BillingController::class, 'downloadInvoice']);
    Route::get('/billings/quotation/download{id}', [BillingController::class, 'downloadQuotation']);
    Route::get('/view-bill/{bill}', [BillingController::class, 'show'])->name('showBill');
    Route::get('/print-bill/{bill}/{action}', [BillingController::class, 'billPdf'])->name('print-pdf');
});



Route::prefix('leaves')->group(function () {
    Route::get('/', [LeaveController::class, 'index'])->name('leaves')->middleware(['auth']);
    Route::get('/leavesData', [LeaveController::class, 'leavesData'])->name('leavesData')->middleware(['auth']);
    Route::post('/mass-approve', [LeaveController::class, 'massApprove'])->name('mass-approve')->middleware(['auth']);
    Route::post('/mass-disapprove', [LeaveController::class, 'massDisapprove'])->name('mass-disapprove')->middleware(['auth']);
    Route::post('/approve/{id}', [LeaveController::class, 'approve'])->name('leaves.approve')->middleware(['auth']);
    Route::post('/disapprove/{id}', [LeaveController::class, 'disapproveLeave'])->name('leaves.disapprove')->middleware(['auth']);
    Route::get('/leaveDetails/{id}', [LeaveController::class, 'show'])->name('leaveDetails')->middleware(['auth']);
});







//employee routes
Route::get('/add-employee', AddEmployee::class)->name('add-employees')->middleware(['auth']);
Route::get('/view-employee/{id}', ViewEmployee::class)->name('view-employee')->middleware(['auth']);
Route::get('/update-employee/{id}', UpdateEmployee::class)->name('update-employee')->middleware(['auth']);

Route::get('/clients', [ClientController::class, 'index'])->middleware(['auth']);
Route::get('/addclient', AddClient::class)->middleware(['auth']);
Route::get('/view-client/{id}', [ClientController::class, 'show'])->name('view-client')->middleware(['auth']);
Route::get('/update-client/{id}', UpdateClient::class)->name('update-employee')->middleware(['auth']);

Route::get('/payroll', Payroll::class)->middleware(['auth']);
Route::get('/export-payroll/{client}/{type}', [PayrollController::class, 'exportPayroll'])->name('export-payroll')->middleware(['auth']);
Route::get('/view-payroll/{payroll}', [PayrollController::class, 'show'])->name('show-payroll')->middleware(['auth']);
Route::get('/export-payroll/{client}/{type}', [PayrollController::class, 'exportPayroll'])->name('export-payroll')->middleware(['auth']);
Route::post('/change-payroll-status', [PayrollController::class, 'status'])->name('change-payroll-status')->middleware(['auth']);
Route::get('/view-employee-payroll/{employee}/{payroll}/{payslip}', [PayrollController::class, 'viewEmployeePayroll'])->name('viewEmployeePayroll')->middleware(['auth']);

Route::get('/attendances', Attendances::class)->middleware(['auth']);

Route::get('/tax-rates', TaxRateList::class)->middleware(['auth']);
Route::get('/add-tax-rate', AddTaxRate::class)->middleware(['auth']);

Route::get('/notifications', Notifications::class)->middleware(['auth']);

Route::get('/reports', Reports::class)->middleware(['auth']);

Route::get('/users', UserList::class)->middleware(['auth']);
Route::get('/add-user', AddUser::class)->middleware(['auth']);

Route::get('/settings', [SettingsController::class, 'index'])->middleware(['auth'])->name('settings');


//PDF ROUTES
// Route::get('/generate-pdf', [App\Http\Controllers\PDFController::class, 'generatePDF']);
Route::get('/payslip/download/{employeeId}/{payrollId}', [PayslipController::class, 'downloadPayslip'])->name('payslip.download');