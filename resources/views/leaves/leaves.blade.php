@extends('adminlte::page')

@section('title', 'Leave')

@section('content_header')
    <h1 class="text-muted">
        Leave Management
    </h1>
    @push('css')

        <style>
        #leaveDetail.modal {
            position: fixed !important;
        }
        </style>


    @endpush
@stop

@section('content')

<!-- Vue app container for Leave View component -->
<div id="app">
    <div class="row mb-5">
        <div class="col-md-4">
            <!-- Total Requests Card -->
            <div class="card text-white bg-secondary mb-1">
                <div class="card-body">
                    <h5 class="card-title">Pending</h5>
                    <p class="card-text">
                        @{{ pendingCount}}
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <!-- Approved Requests Card -->
            <div class="card text-white bg-secondary mb-1">
                <div class="card-body">
                    <h5 class="card-title">Approved</h5>
                    <p class="card-text">
                        @{{ approvedCount }}
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <!-- Disapproved Requests Card -->
            <div class="card text-white bg-secondary mb-1">
                <div class="card-body">
                    <h5 class="card-title">Disapproved</h5>
                    <p class="card-text">
                        @{{ disapprovedCount }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row card mt-5 pt-5">
        <table id="leavesTable" class="table table-bordered table-striped table-vcenter">
            <thead>
                <tr>
                    <th style="min-width: 12em;">Employee</th>
                    <th style="min-width: 8em;">Start Date</th>
                    <th style="min-width: 12em;">Type</th>
                    <th style="min-width: 12em;">Reason</th>
                    <th style="min-width: 8em;">Status</th>
                    <th style="min-width: 8em;">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="leave in leaves" :key="leave.id">
                    <!-- Employee Name -->
                    <td>
                        <span v-if="leave.employee">
                            @{{ leave.employee.fname || '' }}
                            @{{ leave.employee.mname || '' }}
                            @{{ leave.employee.sname || '' }}
                        </span>
                        <span v-else class="text-muted">No Employee</span>
                    </td>

                    <!-- Start Date -->
                    <td>@{{ leave.start_date }}</td>

                    <!-- Leave Type -->
                    <td>@{{ leave.type }}</td>

                    <!-- Reason -->
                    <td>@{{ leave.reason }}</td>

                    <!-- Status with Badge -->
                    <td>
                        <span :class="{
                            'badge bg-success': leave.status === 'Approved',
                            'badge bg-danger': leave.status === 'Disapproved',
                            'badge bg-warning text-dark': leave.status === 'Pending'
                        }">
                            @{{ leave.status }}
                        </span>
                    </td>

                    <!-- Actions Dropdown -->
                    <td>
                        <div class="dropdown">
                            <button
                                class="btn btn-primary dropdown-toggle btn-sm"
                                type="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                            >
                                Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item text-success" href="#" @click.prevent="approveLeave(leave.id)">
                                        Approve
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger" href="#" @click.prevent="disapproveLeave(leave.id)">
                                        Disapprove
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-info" href="#" data-bs-toggle="modal" data-bs-target="#leaveDetail" @click.prevent="viewLeaveDetails(leave)">
                                        View
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

    </div>

    {{-- modal code here --}}
    <div
        class="modal fade text-left"
        id="leaveDetail"
        tabindex="-1"
        role="dialog"
        aria-hidden="true"
        v-show="selectedLeave"
    >

    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ _('Leave Details') }}</h4>
                <button
                    type="button"
                    class="close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                    @click="closeModal"
                    >
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div>
                    <div class="row p-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body ">
                                        <div class="row">
                                            <div class="col md-6">
                                                <div class="card p-2">
                                                   <h5>Employee ID</h5>
                                                    <p>  @{{ selectedLeave.employee_no }}</p>
                                                </div>
                                            </div>

                                            <div class="col md-6">
                                                <div class="card p-2">
                                                    <h5>Name</h5>
                                                    <p>@{{ selectedLeave.fname }}  @{{ selectedLeave.mname }}  @{{ selectedLeave.sname }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <h6>Leave Type</h6>
                                                <p class="card p-2">@{{ selectedLeave.type }}</p>
                                            </div>
                                            <div class="col-md-4">
                                                <h6>Status</h6>
                                                <p class="card p-2">@{{ selectedLeave.status }}</p>
                                            </div>
                                            <div class="col-md-4">
                                                <h6>Application Date</h6>
                                                <p class="card p-2"> @{{ selectedLeave.start_date }}</p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h6>Reason here</h6>
                                                <p class="card p-2">@{{ selectedLeave.reason }}</p>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h6>Leave Records</h6>
                                                <p>records here</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>



</div>
{{-- @include('leaves.leaveDetail') --}}
@endsection

@include('/components/layouts/footer_bottom')

{{-- @push('js')
    <script>
        $(document).ready(function() {
            $('#LeaveTable').DataTable({
                autoWidth: false,
                responsive: true
                // paging: true,
                // search: true,
                // ordering: true,
                // info: true,
                // lengthChange: true,
                // pageLength: 10,
            });
        });
    </script>
@endpush --}}

@push('js')
    <script>
const app = createApp({
    setup() {
        // Axios CSRF token setup
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute('content');

        const leaves = ref([]);  // Ensures it's an array
        const selectAll = ref(false);
        const selectedLeave = ref({}); // Initialized as an object
        const statusCounts = ref({
            Approved: 0,
            Disapproved: 0,
            Pending: 0,
        });

const fetchLeaveData = async () => {
    NProgress.start();
    try {
        const response = await axios.get(`/leaves/leavesData`);
        console.log("API Response:", response.data); // Debugging
        if (!Array.isArray(response.data)) {
            throw new Error("Invalid API response: Expected an array");
        }
        leaves.value = response.data;
        initializeDataTable();
    } catch (error) {
        console.error('Error fetching leave data:', error);
    } finally {
        NProgress.done();
    }
};



        const pendingCount = computed(() => {
            return leaves.value.filter(leave => leave.status === 'Pending').length;
        });
        const approvedCount = computed(() => {
            return leaves.value.filter(leave => leave.status === 'Approved').length;
        });
        const disapprovedCount = computed(() => {
            return leaves.value.filter(leave => leave.status === 'Disapproved').length;
        });




        const viewLeaveDetails = (leave) => {
            if (leave && leave.employee) {
                selectedLeave.value = {
                    ...leave,  // Copy leave details
                    employee_no: leave.employee.employee_no, // Ensure correct mapping
                    fname: leave.employee.fname,
                    mname: leave.employee.mname,
                    sname: leave.employee.sname
                };
                $('#leaveDetail').modal('show');
            } else {
                console.error("viewLeaveDetails called with null or invalid leave object.");
            }
        };


        const closeModal = () => {
            selectedLeave.value = {}; // Reset to an empty object
        };

        const toggleSelectAll = () => {
            selectAll.value = !selectAll.value;
            leaves.value.forEach(leave => {
                leave.selected = selectAll.value;
            });
        };

        const toggleDropdown = (leave) => {
            leave.showDropdown = !leave.showDropdown;
            leaves.value.forEach((l) => {
                if (l.id !== leave.id) l.showDropdown = false;
            });
        };

        const getSelectedIds = () => {
            return leaves.value.filter(leave => leave.selected).map(leave => leave.id);
        };

        const processLeaves = (action) => {
            const selectedIds = getSelectedIds();
            if (selectedIds.length === 0) {
                notification('Please select at least one leave request.', 'error');
                return;
            }
            axios.post(`/leaves/${action}`, { ids: selectedIds })
                .then(response => {
                    updateCounts(response.data);
                    notification(`Mass ${action} successful!`, 'success');
                    fetchLeaveData();
                })
                .catch(error => console.error(`Error in ${action}:`, error));
        };

        const notification = ($text, $icon) => {
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
        };

 const approveLeave = async (id) => {
    try {
        const response = await axios.post(`{{ route('leaves.approve', '') }}/${id}`);
        updateCounts(response.data);
        notification(`Approval successful!`, 'success');

        // Reload only the DataTable's data without reinitialization
        $('#leavesTable').DataTable().ajax.reload(null, false);
    } catch (error) {
        console.error('Error approving leave:', error);
    }
};

const disapproveLeave = async (id) => {
    try {
        const response = await axios.post(`{{ route('leaves.disapprove', '') }}/${id}`);
        updateCounts(response.data);
        notification(`Disapproval successful!`, 'success');

        // Reload only the DataTable's data without reinitialization
        $('#leavesTable').DataTable().ajax.reload(null, false);
    } catch (error) {
        console.error('Error disapproving leave:', error);
    }
};





        const updateCounts = (data) => {
            statusCounts.value = {
                Approved: data.approvedRequests || 0,
                Disapproved: data.disapprovedRequests || 0,
                Pending: data.pendingRequests || 0,
            };
        };


  const initializeDataTable = () => {
    try {
        let table = $('#leavesTable').DataTable();
        if (table) {
            table.clear().destroy(); // Ensure only one instance exists
        }

        Vue.nextTick(() => {
            $('#leavesTable').DataTable({
                dom: 'Bfrtip',
                buttons: ['copy', 'excel', 'pdf', 'print'],
                scrollX: true,
                scrollY: true,
                paging: true,
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                ordering: true,
                autoWidth: false,
                ajax: {
                    url: "{{ route('leaves') }}",
                    type: "GET",
                    dataSrc: ""
                },
                columns: [
                    { data: 'id' },
                    { data: 'employee_name' },
                    { data: 'leave_type' },
                    { data: 'status' },
                    { data: 'actions' }
                ],
                initComplete: function() {
                    console.log("DataTable initialized successfully.");
                }
            });
        });
    } catch (error) {
        console.error('Error initializing DataTable:', error);
    }
};





        onMounted(() => {
            fetchLeaveData();
            // initializeDataTable();
        });

        return {
            leaves,
            selectAll,
            statusCounts,
            fetchLeaveData,
            toggleSelectAll,
            toggleDropdown,
            getSelectedIds,
            approveLeave,
            disapproveLeave,
            selectedLeave,
            viewLeaveDetails,
            closeModal,
            initializeDataTable,
            pendingCount,
            approvedCount,
            disapprovedCount


        };
    },
});

app.mount('#app');
</script>

@endpush

