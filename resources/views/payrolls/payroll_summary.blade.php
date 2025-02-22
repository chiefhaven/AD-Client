@extends('adminlte::page')

@section('title', 'Payrolls')

@section('content_header')
    @hasSection('content_header_title')
        <h1 class="text-muted">
            @yield('content_header_title', 'adminlte')

            @hasSection('content_header_subtitle')
                <small class="text-dark">
                    <i class="fas fa-xs fa-angle-right text-muted"></i>
                    @yield('content_header_subtitle')
                </small>
            @endif
        </h1>
    @endif
@stop

@section('content')
<livewire:common.page-header pageTitle="Payrolls"/>

<div id="payrolls">
  <div class="col">
    <!-- Payroll Table -->
    <div>

    <div class="table-responsive" id="viewClient">
        @if( !$payrolls->isEmpty())
          <table id="payrollTable" class="table table-bordered table-striped table-vcenter display nowrap">
              <thead>
                  <tr>
                      <th style="min-width: 150px;">Payroll Period</th>
                      <th style="min-width: 50px;">Employees</th>
                      <th style="min-width: 150px;">Total gross</th>
                      <th style="min-width: 150px;">Total Paye</th>
                      <th style="min-width: 150px;">Total net</th>
                      <th style="min-width: 150px;">Total earnings</th>
                      <th style="min-width: 150px;">Total deductions</th>
                      <th style="min-width: 150px;">Total paid</th>
                      <th>Status</th>
                      <th class="text-center" style="width: 100px;">Actions</th>
                  </tr>
              </thead>
              <tbody>
                @foreach ($payrolls as $payroll)
                  <tr>
                    <td class="font-w600">
                       {{ $payroll->group }}
                    </td>
                    <td class="font-w600">
                        @if(isset($payroll->employees))
                            {{ $payroll->employees()->count() }}
                        @endif
                    </td>
                    <td class="font-w600">
                        K{{ number_format($payroll->employees()->sum('payroll_employee.salary')) }}
                    </td>
                    <td>
                        K{{ number_format($payroll->employees()->sum('payroll_employee.payee')) }}
                    </td>
                    <td class="font-w600">
                        K{{ number_format($payroll->employees()->sum('payroll_employee.net_salary')) }}
                    </td>
                    <td>
                        K{{ $payroll->employees()->sum('payroll_employee.earning_amount') }}
                    </td>
                    <td class="font-w600">
                        K{{ number_format($payroll->employees()->sum('payroll_employee.deduction_amount')) }}
                    </td>
                    <td>
                        K{{ number_format($payroll->employees()->sum('payroll_employee.total_paid')) }}
                    </td>
                    <td>
                        @if ($payroll->status === 'Paid' || $payroll->status === 'Cancelled')
                            {{ $payroll->status }}
                        @else
                            <button class="btn btn-sm btn-warning" @click="openStatusDialog('{{ $payroll->id }}', '{{ $payroll->status }}')">
                                {{ $payroll->status }}
                            </button>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="dropdown d-inline-block">
                            <button type="button" class="btn btn-default" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="d-sm-inline-block">Action</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end p-0">
                                <div class="p-2">
                                    <!-- View Payroll Link -->
                                    <button class="dropdown-item nav-main-link" @click="fetchPayrollDetails('{{ $payroll->id }}')">
                                        <i class="nav-main-link-icon fas fa-eye"></i>
                                        <span class="btn">View</span>
                                    </button>

                                    <!-- Export Payroll -->
                                    <a class="dropdown-item nav-main-link btn" href="{{ route('export-payroll', [$payroll->id, 'pdf']) }}">
                                        <i class="nav-main-link-icon fas fa-print"></i>
                                        <span class="btn">Export PDF</span>
                                    </a>

                                    <!-- Export Payroll -->
                                    <a class="dropdown-item nav-main-link btn" href="{{ route('export-payroll', [$payroll->id, 'csv']) }}">
                                        <i class="nav-main-link-icon fas fa-print"></i>
                                        <span class="btn">Export CSV</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
          </table>

          @include('../payrolls/includes/viewPayrollModal')

        @else
            <p class="p-5">
                No payrolls yet for this client!
            </p>
        @endif
    </div>
    </div>
  </div>
</div>
@stop

@include('components.layouts.footer_bottom')

@push('js')
<!-- Vue App Script -->
<script type="module" src="{{ asset('js/payroll.js') }}"></script>

<script>
    $(document).ready(function() {
        $('#payrollTable').DataTable({
            dom: 'Bfrtip',
                buttons: [
                    'copy', 'excel', 'pdf', 'print'
                ],
            scrollX: true,
            scrollY: true,
        });
    });
</script>

<script>

    const app = createApp({
      setup() {
        const showPayrollModal = ref(false);  // Modal hidden by default
        const showEmployeePayModal = ref(false);  // Modal hidden by default
        const data = ref(null);        // Store payroll data
        const employeeData = ref(null);        // Store employee payroll data
        const loading = ref(false);    // Manage loading state
        const error = ref(null);       // Store error messages
        const showAddPayrollModal = ref(false);

        const openAddPayrollModal = (client) =>{
            showAddPayrollModal.value = true;
            fetchAddPayrollDetails(client);
        }

        // Close the modal
        const closeAddPayrollModal = () => {
            data.value = null
            showAddPayrollModal.value = false;
        };

        const fetchAddPayrollDetails = async (client) =>{
            NProgress.start();
            error.value = null;
            try {
                const response = await axios.get(`/add-payroll/${client}`);
                if (response.data && response.data.length > 0) {
                    data.value = response.data[0];  // Access the first object in the response array

                } else {
                    error.value = "No data found for the provided payroll ID.";
                }
            } catch (err) {
                error.value = "Failed to fetch payroll data";
            } finally {
                loading.value = false;
                NProgress.done();
            }
        }

        // Open the payroll details modal and fetch payroll data
        const fetchPayrollDetails = (payroll) => {
            NProgress.start();
            showPayrollModal.value = true;
            fetchData(payroll);  // Fetch data when the modal opens
        };

        // Close the modal
        const closeModal = () => {
            data.value = null
            showPayrollModal.value = false;
        };

        // Fetch Payroll details
        const fetchData = async (payroll) => {
            loading.value = true;
            error.value = null;
            try {
                const response = await axios.get(`/view-payroll/${payroll}`);
                if (response.data && response.data.length > 0) {
                    data.value = response.data[0];  // Access the first object in the response array

                } else {
                    error.value = "No data found for the provided payroll ID.";
                }
            } catch (err) {
                error.value = "Failed to fetch payroll data";
            } finally {
                loading.value = false;
                NProgress.done();
            }
        };

        // Open the payroll details modal and fetch payroll data
        const employeePayDetails = (employee, payroll) => {
            NProgress.start();
            showEmployeePayModal.value = true;
            showPayrollModal.value = false;
            fetchEmployeePayDetails(employee, payroll);  // Fetch data when the modal opens
        };

        // Open the payroll details modal and fetch payroll data
        const employeePayslip = (employee, payroll, payslip) => {
            loading.value = true;
            fetchEmployeePayDetails(employee, payroll, payslip);
        };

        // Fetch Employee Payroll details
        const fetchEmployeePayDetails = async (employee, payroll, payslip) => {
            loading.value = true;
            error.value = null;

            try {
                // Check if payslip is equal to 1 and open in a new window for PDF download
                if (payslip == '1') {
                    // Open the download route in a new window
                    window.open(`/view-employee-payroll/${employee}/${payroll}/${payslip}`, '_blank');
                    return; // Exit the function after opening the new window
                }

                const response = await axios.get(`/view-employee-payroll/${employee}/${payroll}/${payslip}`);

                // Check if the response data is an object and contains the expected properties
                if (response.data) {
                    // Ensure that data is in the expected format
                    if (Array.isArray(response.data) && response.data.length > 0) {
                        employeeData.value = response.data[0];  // Access the first object in the response array
                    } else {
                        loading.value = false;
                    }
                } else {
                    error.value = "No data found for the provided payroll ID.";
                }
            } catch (err) {
                error.value = "Failed to fetch payroll data";
                console.error(err); // Log the error for debugging purposes
            } finally {
                loading.value = false; // Ensure loading is false after the request completes
                NProgress.done();
            }
        };

        // Close the modal
        const closeEmployeePayModal = () => {
            showEmployeePayModal.value = false;
            showPayrollModal.value = true;
        };

        const changeStatus = (status, payrollId) => {
            Swal.fire({
                title: 'Are you sure?',
                text: `You are about to change the status to ${status}. This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, change it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Make an API call to change the status
                    axios.post('/change-payroll-status', { status, payroll: payrollId })
                        .then(response => {
                            notification('Payroll status updated successfully, page', 'success');
                                    // Refresh the page
                            location.reload(); // Reloads the current page
                        })
                        .catch(error => {
                            notification('Failed to update payroll status.', 'error');
                        });
                }
            });
        };

        const openStatusDialog = (payrollId, currentStatus) => {
            Swal.fire({
                title: 'Change Payroll Status',
                text: `Current status: ${currentStatus}`,
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Approve',
                showDenyButton: true,
                denyButtonText: 'Reject',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    changeStatus('Approved', payrollId);
                } else if (result.isDenied) {
                    changeStatus('Rejected', payrollId);
                }
            });
        };

        // Computed properties for different totals
        const totalGross = computed(() => {
            const total = data.value?.employees?.reduce((sum, emp) => sum + parseFloat(emp.pivot.salary || 0), 0) || 0;

            return formatCurrency(total);
        });

        const totalNet = computed(() => {
            const total = data.value?.employees?.reduce((sum, emp) => sum + parseFloat(emp.pivot.net_salary || 0), 0) || 0;

            return formatCurrency(total);
        });

        const totalPaye = computed(() => {
            const total = data.value?.employees?.reduce((sum, emp) => sum + parseFloat(emp.pivot.payee || 0), 0) || 0;

            return formatCurrency(total);
        });

        const totalPaid = computed(() => {
            const total = data.value?.employees?.reduce((sum, emp) => sum + parseFloat(emp.pivot.total_paid || 0), 0) || 0;

            return formatCurrency(total);
        });

        const totalDeductions = computed(() => {
            const total = data.value?.employees?.reduce((sum, emp) => sum + parseFloat(emp.pivot.deduction_amount || 0), 0) || 0;

            return formatCurrency(total);
        });

        const formatCurrency = (value) => {
            return `K ${Number(value).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        };

        const totalEarnings = computed(() => {
            const total = data.value?.employees?.reduce((sum, emp) => sum + parseFloat(emp.pivot.earning_amount || 0), 0) || 0;

            return formatCurrency(total);
        });

        const proceed = () => {
            // Add your logic to proceed here
        };

        const notification = ($text, $icon) =>{
            Swal.fire({
                toast: true,
                position: "top-end",
                html: $text,
                showConfirmButton: false,
                timer: 5500,
                timerProgressBar: true,
                icon: $icon,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                  }
              });
        }

        return {
            showPayrollModal,
            data,
            employeeData,
            loading,
            error,
            totalGross,
            totalNet,
            totalPaye,
            totalPaid,
            totalDeductions,
            totalEarnings,
            closeModal,
            proceed,
            fetchPayrollDetails,
            closeEmployeePayModal,
            showEmployeePayModal,
            employeePayDetails,
            formatCurrency,
            changeStatus,
            openStatusDialog,
            employeePayslip,
            closeAddPayrollModal,
            openAddPayrollModal,
            showAddPayrollModal,
        };
      },
    });

    app.mount('#payrolls');
</script>
@endpush
