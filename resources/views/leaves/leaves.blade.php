@section('title', 'Leave')

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
<div v-cloak id="app">
    {{-- <livewire:common.page-header pageTitle="Leaves"/> --}}
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

    <div class="row card mt-5 pt-5 p-3">
        <table id="leavesTable"  class="table table-bordered table-striped table-vcenter display nowrap">
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
                                                    <p>  @{{ selectedLeave.employee?.employee_no }}</p>
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
                                                <h6>Start Date</h6>
                                                <p class="card p-2"> @{{ selectedLeave.start_date }}</p>
                                            </div>
                                            <div class="col-md-4">
                                                <h6>End Date</h6>
                                                <p class="card p-2"> @{{ selectedLeave.end_date }}</p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h6>Reason</h6>
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
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>


<script>
const app = createApp({
    setup() {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute('content');

        const leaves = ref([]);
        const selectAll = ref(false);
        const selectedLeave = ref({});
        const statusCounts = ref({
            Approved: 0,
            Disapproved: 0,
            Pending: 0,
        });

        const fetchLeaveData = async () => {
            NProgress.start();
            try {
                const response = await axios.get(`/leaves/leavesData`);
                console.log("API Response:", response.data);

                if (!response.data || !Array.isArray(response.data.leaves)) {
                    throw new Error("Invalid API response: Expected an object with 'leaves' array");
                }

                leaves.value = response.data.leaves;
                updateCounts(response.data.statusCounts); // ✅ Correctly update status counts
                initializeDataTable();
            } catch (error) {
                console.error('Error fetching leave data:', error);
            } finally {
                NProgress.done();
            }
        };

        const updateCounts = (statusData) => {
            statusCounts.value = {
                Approved: statusData?.Approved || 0,
                Disapproved: statusData?.Disapproved || 0,
                Pending: statusData?.Pending || 0,
            };
        };

        const approveLeave = async (id) => {
            try {
                const response = await axios.post(`{{ route('leaves.approve', '') }}/${id}`);
                updateCounts(response.data.statusCounts); // ✅ Ensure counts update correctly
                notification(`Approval successful!`, 'success');
                $('#leavesTable').DataTable().ajax.reload(null, false); // ✅ Reload only data
            } catch (error) {
                console.error('Error approving leave:', error);
            }
        };

        const disapproveLeave = async (id) => {
            try {
                const response = await axios.post(`{{ route('leaves.disapprove', '') }}/${id}`);
                updateCounts(response.data.statusCounts); // ✅ Ensure counts update correctly
                notification(`Disapproval successful!`, 'success');
                $('#leavesTable').DataTable().ajax.reload(null, false); // ✅ Reload only data
            } catch (error) {
                console.error('Error disapproving leave:', error);
            }
        };

        const initializeDataTable = () => {
            try {
                let table = $('#leavesTable').DataTable();
                if (table) {
                    table.clear().destroy();
                }

                Vue.nextTick(() => {
                    $('#leavesTable').DataTable({
                        dom: 'Bfrtip',
                        buttons: ['copy', 'excel', 'pdf', 'print'],
                        scrollX: true,
                        paging: true,
                        pageLength: 10,
                        ajax: {
                            url: "{{ route('leaves') }}", // ✅ Ensure correct endpoint
                            type: "GET",
                            dataSrc: (json) => {
                                updateCounts(json.statusCounts); // ✅ Update counts with each reload
                                return json.leaves || [];
                            }
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

         onMounted(async () => {
            await fetchLeaveData(); // Ensure data is fetched first
            initializeDataTable();  // Then initialize DataTables
        });

        return {
            leaves,
            selectAll,
            statusCounts,
            fetchLeaveData,
            approveLeave,
            disapproveLeave,
            initializeDataTable
        };
    },
});

app.mount('#app');
</script>
@endpush
