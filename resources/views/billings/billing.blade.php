@extends('adminlte::page')

{{-- Extend and customize the browser title --}}

@section('title', 'Billing')




{{-- Extend and customize the page content header --}}

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

{{-- Rename section content to content_body --}}

@section('content')
<div class="row" id="bills">
    <livewire:common.page-header pageTitle="Invoices"/>
    @if (!$bills->where('billing_type', 'invoice')->isEmpty())
        <div class="card mb-3 p-4 mb-5">
            <h4 class="pb-4">All invoices</h4>
            <div class="col-md-12">
                <table id="billInvoicesTable" class="table table-responsive table-bordered table-striped table-vcenter display nowrap">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 100px;">Actions</th>
                            <th style="min-width: 150px;">Date</th>
                            <th style="min-width: 150px;">Due Date</th>
                            <th style="min-width: 90px;">Status</th>
                            <th style="min-width: 150px;">Invoice No.</th>
                            <th class="text-end" style="min-width: 150px;">Items Qty</th>
                            <th class="text-end" style="min-width: 150px;">Item Total</th>
                            <th class="text-end" style="min-width: 150px;">Discount</th>
                            <th class="text-end" style="min-width: 150px;">Tax</th>
                            <th class="text-end" style="min-width: 150px;">Grand Total</th>
                            <th class="text-end" style="min-width: 150px;">Total Paid</th>
                            <th class="text-end" style="min-width: 150px;">Balance</th>
                            <th style="min-width: 100px;">Payment Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bills->where('billing_type', 'invoice') as $bill)
                            @php
                                $balance = $bill->products->sum('pivot.total') - $bill->payments->sum('payment_amount');
                                $paidAmount = $bill->payments->sum('payment_amount');
                            @endphp
                            <tr>
                                <!-- Actions Dropdown -->
                                <td class="text-center">
                                    <div class="dropdown d-inline-block">
                                        <button type="button" class="btn btn-default" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="d-sm-inline-block">Action</span>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end p-0">
                                            <div class="p-2">
                                                <a class="dropdown-item nav-main-link" href="#" @click="viewBill('{{ $bill->id }}')">
                                                    <i class="nav-main-link-icon fas fa-eye"></i> View
                                                </a>
                                                <a class="dropdown-item nav-main-link" href="#" @click="printBill('{{ $bill->id }}', 'print')">
                                                    <i class="nav-main-link-icon fas fa-print"></i> Print Bill
                                                </a>
                                                <a class="dropdown-item nav-main-link" href="#" @click="printBill('{{ $bill->id }}', 'pdf')">
                                                    <i class="nav-main-link-icon fas fa-file"></i> Export PDF
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Date -->
                                <td>{{ \Carbon\Carbon::parse($bill->billing_date)->format('d F, Y') }}</td>

                                <!-- Due Date -->
                                <td>
                                    @isset($bill->billing_due_date)
                                        {{ \Carbon\Carbon::parse($bill->billing_due_date)->format('d F, Y') }}
                                    @else
                                        Coming Soon
                                    @endisset
                                </td>

                                <!-- Status with Badge -->
                                <td class="text-end">
                                    <span class="badge
                                        @if($bill->status == 'Pending') badge-warning
                                        @elseif($bill->status == 'Paid') badge-success
                                        @else badge-secondary
                                        @endif">
                                        {{ ucfirst($bill->status) }}
                                    </span>
                                </td>

                                <!-- Invoice Number -->
                                <td>{{ $bill->invoice_number }}</td>

                                <!-- Items Quantity -->
                                <td class="text-end">{{ $bill->products->sum('pivot.quantity') }}</td>

                                <!-- Item Total -->
                                <td class="text-end">
                                    K{{ number_format($bill->products->sum(fn($product) => $product->pivot->price * $product->pivot->quantity), 2) }}
                                </td>

                                <!-- Discount -->
                                <td class="text-end">K{{ number_format($bill->products->sum('pivot.item_discount'), 2) }}</td>

                                <!-- Tax -->
                                <td class="text-end">K{{ number_format($bill->products->sum('pivot.tax'), 2) }}</td>

                                <!-- Grand Total -->
                                <td class="text-end">K{{ number_format($bill->products->sum('pivot.total'), 2) }}</td>

                                @if ($bill->billing_type == 'invoice')
                                    <!-- Total Paid -->
                                    <td class="text-end">K{{ number_format($paidAmount, 2) }}</td>

                                    <!-- Balance -->
                                    <td class="text-end">K{{ number_format($balance, 2) }}</td>

                                    <!-- Payment Status -->
                                    <td>
                                        <button class="btn btn-sm
                                            @if ($balance == 0)
                                                btn-success
                                            @elseif ($balance > 0 && $paidAmount > 0)
                                                btn-primary
                                            @else
                                                btn-warning
                                            @endif">
                                            @if ($balance == 0)
                                                Paid
                                            @elseif ($balance > 0 && $paidAmount > 0)
                                                Partial Payment
                                            @else
                                                Due
                                            @endif
                                        </button>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="card col-12 text-center py-4">
            <p class="text-muted h5">No invoices yet!</p>
        </div>
    @endif
    <livewire:common.page-header pageTitle="Quotations"/>

    @if (!$bills->where('billing_type', 'quotation')->isEmpty())
        <div class="col-md-12 card mb-3 p-4">
            <h4 class="pb-4">All quotations</h4>
                <table id="billQuotationTable" class="table table-responsive table-bordered table-striped table-vcenter display nowrap">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 100px;">Actions</th>
                            <th style="min-width: 150px;">Date</th>
                            <th style="min-width: 150px;">Due Date</th>
                            <th style="min-width: 90px;">Status</th>
                            <th style="min-width: 150px;">Invoice No.</th>
                            <th class="text-end" style="min-width: 150px;">Items Qty</th>
                            <th class="text-end" style="min-width: 150px;">Item Total</th>
                            <th class="text-end" style="min-width: 150px;">Discount</th>
                            <th class="text-end" style="min-width: 150px;">Tax</th>
                            <th class="text-end" style="min-width: 150px;">Grand Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bills->where('billing_type', 'quotation') as $bill)
                            @php
                                $balance = $bill->products->sum('pivot.total') - $bill->payments->sum('payment_amount');
                                $paidAmount = $bill->payments->sum('payment_amount');
                            @endphp
                            <tr>
                                <!-- Actions Dropdown -->
                                <td class="text-center">
                                    <div class="dropdown d-inline-block">
                                        <button type="button" class="btn btn-default" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="d-sm-inline-block">Action</span>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end p-0">
                                            <div class="p-2">
                                                <a class="dropdown-item nav-main-link" href="#" @click="viewBill('{{ $bill->id }}')">
                                                    <i class="nav-main-link-icon fas fa-eye"></i> View
                                                </a>
                                                <a class="dropdown-item nav-main-link" href="#" @click="printBill('{{ $bill->id }}', 'print')">
                                                    <i class="nav-main-link-icon fas fa-print"></i> Print Bill
                                                </a>
                                                <a class="dropdown-item nav-main-link" href="#" @click="printBill('{{ $bill->id }}', 'pdf')">
                                                    <i class="nav-main-link-icon fas fa-file"></i> Export PDF
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Date -->
                                <td>{{ \Carbon\Carbon::parse($bill->billing_date)->format('d F, Y') }}</td>

                                <!-- Due Date -->
                                <td>
                                    @isset($bill->billing_due_date)
                                        {{ \Carbon\Carbon::parse($bill->billing_due_date)->format('d F, Y') }}
                                    @else
                                        Coming Soon
                                    @endisset
                                </td>

                                <!-- Status with Badge -->
                                <td class="text-end">
                                    <span class="badge
                                        @if($bill->status == 'Pending') badge-warning
                                        @elseif($bill->status == 'Paid') badge-success
                                        @else badge-secondary
                                        @endif">
                                        {{ ucfirst($bill->status) }}
                                    </span>
                                </td>

                                <!-- Invoice Number -->
                                <td>{{ $bill->invoice_number }}</td>

                                <!-- Items Quantity -->
                                <td class="text-end">{{ $bill->products->sum('pivot.quantity') }}</td>

                                <!-- Item Total -->
                                <td class="text-end">
                                    K{{ number_format($bill->products->sum(fn($product) => $product->pivot->price * $product->pivot->quantity), 2) }}
                                </td>

                                <!-- Discount -->
                                <td class="text-end">K{{ number_format($bill->products->sum('pivot.item_discount'), 2) }}</td>

                                <!-- Tax -->
                                <td class="text-end">K{{ number_format($bill->products->sum('pivot.tax'), 2) }}</td>

                                <!-- Grand Total -->
                                <td class="text-end">K{{ number_format($bill->products->sum('pivot.total'), 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
        </div>
    @else
        <div class="card col-12 text-center py-4">
            <p class="text-muted h5">No quotations yet!</p>
        </div>
    @endif

    @include('billings/includes/viewBillModal')
</div>

@push('js')

@endpush

@push('js')
    <script>
        $(document).ready(function() {
            $('#billInvoicesTable').DataTable({
                autoWidth: true,
                responsive: true,
                dom: 'Bfrtip',
                    buttons: [
                        'copy', 'excel', 'pdf', 'print'
                    ],
                scrollX: true,
                scrollY: true,
            });

            $('#billQuotationTable').DataTable({
                autoWidth: true,
                responsive: true,
                dom: 'Bfrtip',
                    buttons: [
                        'copy', 'excel', 'pdf', 'print'
                    ],
                scrollX: true,
                scrollY: true,
            });
        });

        const bills = createApp({
            setup() {
                // Reactive references
                const billData = ref();
                const showBillModal = ref(false);
                const showAddPaymentModal = ref(false);
                const state = ref({
                    notes: '',
                    payment_method: '',
                    amountToPay: 0,
                    paidAmount: 0,
                    chequeAccountNumber: '',
                    payment_date: new Date().toISOString().split('T')[0],
                })


                const confirmBillDelete = async(billId) => {
                    Swal.fire({
                        title: 'Delete bill?',
                        text: 'Do you want to delete this bill? This action cannot be undone!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Delete',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            deleteBill(billId);
                        }
                    });
                };

                const formatCurrency = (value) => {
                    return `K ${Number(value).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                };

                const formatDate = (date) => {
                    if (!date) return '';
                    return new Intl.DateTimeFormat('en-US', {
                      year: 'numeric',
                      month: 'long',
                      day: 'numeric',
                    }).format(new Date(date));
                };

                const deleteBill = async (bill) => {
                    NProgress.start();

                    try {
                        const response = await axios.delete(`/delete-bill/${bill}`);

                        if (response.status === 200) {
                            notification('Bill deleted successfully', 'success');
                            window.location.reload();
                        }
                    } catch (error) {
                        const errorMessage = error.response.data.message || 'An unexpected error occurred';
                        notification(errorMessage, 'error');
                    } finally {
                        NProgress.done();
                    }
                };

                const viewBill = async(bill) => {

                    NProgress.start();
                    try {
                        const response = await axios.get(`/view-bill/${bill}`);
                        console.log(response)
                        if (response.data) {
                            billData.value = response.data;
                            showBillModal.value = true;

                        } else {
                            notification('No data found.', 'error');
                        }
                    } catch (err) {
                        notification('Failed to fetch data', 'error');
                    } finally {
                        NProgress.done();
                    }
                }

                const addPayment = async(bill) => {
                    NProgress.start();
                    try {
                        const response = await axios.get(`/view-bill/${bill}`);
                        console.log(response)
                        if (response.data) {
                            billData.value = response.data;
                            showAddPaymentModal.value = true;
                        } else {
                            notification('Shit happened.', 'error');
                        }
                    } catch (err) {
                        notification('Failed to fetch data', 'error');
                    } finally {
                        NProgress.done();
                    }
                }

                const savePayment = async (bill) => {
                    NProgress.start();
                    try {
                        const response = await axios.post(`/store-payment`, {
                            bill: bill,
                            state: state.value
                        });

                        console.log(response);

                        if (response.data) {
                            state.value = {
                                notes: '',
                                payment_method: '',
                                amountToPay: 0,
                                paidAmount: 0,
                                chequeAccountNumber: '',
                            };

                            showAddPaymentModal.value = false;
                            notification('Bill updated successfully', 'success');
                            window.location.reload();

                        } else {
                            notification('No data found.', 'error');
                        }
                    } catch (err) {
                        notification(erro, 'error');
                        console.error(err); // Log the error for debugging
                    } finally {
                        NProgress.done();
                    }
                };


                // Fetch Payroll details
                const fetchData = async (payroll) => {

                };

                const closeForm = async() =>{
                    showBillModal.value = false;
                    showAddPaymentModal.value = false;
                }

                onMounted(() => {

                });

                const totalAmount = computed(() => {
                    if (Array.isArray(billData.value.products) && billData.value.products.length > 0) {
                      // Sum up the total from each product's pivot data
                      return billData.value.products.reduce((sum, product) => {
                        // Convert `total` to a number to ensure arithmetic operation
                        return sum + (parseFloat(product.pivot.price*product.pivot.quantity) || 0);
                      }, 0);
                    } else {
                      console.warn("No products found");
                      return 0;
                    }
                });

                const totalTax = computed(() => {
                    // Check if products array exists and has items
                    if (Array.isArray(billData.value.products) && billData.value.products.length > 0) {
                        // Sum up the tax from each product's pivot data
                        return billData.value.products.reduce((sum, product) => {
                        // Convert `tax` to a number to ensure arithmetic operation
                        return sum + (parseFloat(product.pivot.tax) || 0);
                        }, 0);
                    } else {
                        console.warn("No products found in billData.");
                        return 0;
                    }
                });

                const totalDiscount = computed(() => {
                    // Check if products array exists and has items
                    if (Array.isArray(billData.value.products) && billData.value.products.length > 0) {
                      // Sum up the discount from each product's pivot data
                      return billData.value.products.reduce((sum, product) => {
                        // Convert `item_discount` to a number to ensure arithmetic operation
                        return sum + (parseFloat(product.pivot.item_discount) || 0);
                      }, 0);
                    } else {
                      console.warn("No products found");
                      return 0;
                    }
                });

                const balance = computed(() => {
                    return totalPayable.value - state.value.amountToPay;
                });

                const totalPayable = computed(() => {
                    if (Array.isArray(billData.value.products) && billData.value.products.length > 0) {

                        const totalPayment = billData.value.payments.reduce((sum, payment) => sum + parseFloat(payment.payment_amount), 0);

                        console.log(totalPayment);
                        // Calculate subtotal by summing all product totals
                        const subtotal = billData.value.products.reduce((sum, product) => {
                            return sum + (parseFloat(product.pivot.price*product.pivot.quantity) || 0);
                        }, 0);

                        // Calculate total discount
                        const totalDiscount = billData.value.products.reduce((sum, product) => {
                            return sum + (parseFloat(product.pivot.item_discount) || 0);
                        }, 0);

                        // Calculate total tax
                        const totalTax = billData.value.products.reduce((sum, product) => {
                            return sum + (parseFloat(product.pivot.tax) || 0);
                        }, 0);

                        // Calculate total payable: (Subtotal - Discount) + Tax
                        return subtotal - totalDiscount + totalTax - totalPayment;
                    } else {
                        console.warn("No products found");
                        return 0;
                    }
                });

                const totalPayments = computed(() => {
                    if (Array.isArray(billData.value.payments) && billData.value.payments.length > 0) {
                      // Calculate total payments by summing up all payment amounts
                      return billData.value.payments.reduce((sum, payment) => {
                        return sum + (parseFloat(payment.payment_amount) || 0);
                      }, 0);
                    } else {
                      return 0;
                    }
                  });

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

                // Helper function to calculate the due date with flexible terms units
                const calculateDueDate = (billingDate, paymentTerms, termsUnit) => {
                    const billingDateObj = new Date(billingDate);

                    if (termsUnit === 'Months') {
                        // Add months to the date
                        billingDateObj.setMonth(billingDateObj.getMonth() + paymentTerms);
                    } else {
                        // Default to days if not 'months'
                        billingDateObj.setDate(billingDateObj.getDate() + paymentTerms);
                    }

                    return formatDate(billingDateObj);
                };

                const printBill = async (bill, action) => {
                    NProgress.start();

                    try {
                        // Open the download route in a new window
                        window.open(`/print-bill/${bill}/${action}`, '_blank');

                    } catch (err) {
                        error.value = "Failed to fetch data";
                    } finally {
                        NProgress.done();
                    }
                };

                return {
                    confirmBillDelete,
                    billData,
                    printBill,
                    viewBill,
                    showBillModal,
                    closeForm,
                    formatCurrency,
                    totalAmount,
                    totalTax,
                    totalDiscount,
                    totalPayable,
                    totalPayments,
                    balance,
                    formatDate,
                    calculateDueDate,
                    addPayment,
                    showAddPaymentModal,
                    state,
                    savePayment,
                };
            }
        });

        bills.mount('#bills');
    </script>
@endpush


@stop


