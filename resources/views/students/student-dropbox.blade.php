<x-FrontLayout>
    <!-- Include Alpine.js and Font Awesome -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.12.0/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <div x-data="studentDropbox()" class="student-dropbox">
        <!-- Loading Spinner -->
        <div class="spinner-container" x-show="loading" x-cloak>
            <div class="spinner-border text-light" style="width: 3rem; height: 3rem;" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <!-- Alert container for flash messages -->
        <div id="alertContainer" class="notification-container">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>

        <div class="container-fluid px-4">
            <!-- Header Section -->
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <h2 class="text-primary mb-0 fw-bold">Student Dropbox Module</h2>
                        </div>
                        <div>
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <form id="filter-form" method="GET" action="{{ route('students.dropbox.filter') }}" class="row g-3">
                        @csrf
                        <!-- District dropdown -->
                        <div class="col-md-4">
                            <label for="parameter1" class="form-label fw-bold">District</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                <select name="parameter1" id="parameter1" class="form-select">
                                    <option value="">-- Select District --</option>
                                    @foreach ($distoption as $option)
                                        <option value="{{ $option['dstcd'] }}" {{ request('parameter1') == $option['dstcd'] ? 'selected' : '' }}>
                                            {{ $option['dname'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Student PEN -->
                        <div class="col-md-4">
                            <label for="parameter3" class="form-label fw-bold">Student PEN</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                <input type="text" class="form-control" name="parameter3" id="parameter3"
                                       value="{{ request('parameter3') }}" placeholder="Enter Student PEN">
                            </div>
                        </div>

                        <!-- UDISE Code -->
                        <div class="col-md-4">
                            <label for="parameter4" class="form-label fw-bold">UDISE Code</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-school"></i></span>
                                <input type="text" class="form-control" name="parameter4" id="parameter4"
                                       value="{{ request('parameter4') }}" placeholder="Enter UDISE Code">
                            </div>
                        </div>

                        <!-- Action buttons -->
                        <div class="col-12 d-flex">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-filter me-1"></i> Apply Filters
                            </button>
                            <a href="{{ route('students.dropbox') }}" class="btn btn-secondary">
                                <i class="fas fa-redo me-1"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Data Section -->
            <div>
                <div class="card shadow-sm">
                    <div class="card-body">
                        @if (isset($rows) && $rows->count() > 0)
                            @php
                                $schname = 'No School';
                                $dstname = 'No District';

                                if (!empty($rw)) {
                                    foreach ($rw as $option) {
                                        $schname = $option['school_name'] ?? 'No School';
                                        $dstname = $option['dstname'] ?? 'No District';
                                    }
                                }
                            @endphp

                                <!-- Transfer Information Alert -->
                            <div class="alert alert-info mb-4">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-info-circle me-3 fs-4"></i>
                                    <div>
                                        <span class="fw-bold">Transfer Destination: </span>
                                        <span>Students will be transferred to <strong>{{ $schname }}</strong> of the district <strong>{{ $dstname }}</strong></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Pagination Summary -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <span class="text-muted">Showing {{ $rows->firstItem() }} to {{ $rows->lastItem() }} of {{ $rows->total() }} students</span>
                                    @if(request('parameter1'))
                                        <span class="badge bg-primary ms-2">
                                            District: {{ collect($distoption)->firstWhere('dstcd', request('parameter1'))['dname'] ?? '' }}
                                        </span>
                                    @endif
                                    @if(request('parameter3'))
                                        <span class="badge bg-info ms-2">PEN: {{ request('parameter3') }}</span>
                                    @endif
                                    @if(request('parameter4'))
                                        <span class="badge bg-secondary ms-2">UDISE: {{ request('parameter4') }}</span>
                                    @endif
                                </div>

                                <!-- Client-side filter for current page -->
                                <div class="search-box">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-filter"></i></span>
                                        <input type="text" class="form-control" id="pageFilter" placeholder="Filter current page...">
                                        <button class="btn btn-outline-secondary" type="button" id="clearPageFilter">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <small class="form-text text-muted">Filters only visible rows (current page)</small>
                                </div>
                            </div>

                            <!-- Table Section -->
                            <div class="table-responsive table-scroll-container">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-primary sticky-top">
                                    <tr>
                                        <th>Sl.No.</th>
                                        <th>Student PEN</th>
                                        <th>Student Name</th>
                                        <th>Father Name</th>
                                        <th>Gender</th>
                                        <th>Date of Birth</th>
                                        <th>Mobile Number</th>
                                        <th>Class</th>
                                        <th>Section</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                        $startingNumber = ($rows->currentPage() - 1) * $rows->perPage() + 1;
                                    @endphp

                                    @foreach ($rows as $index => $row1)
                                        <tr class="student-row {{ in_array($row1['stud_status'], ['E', 'P']) ? 'table-success' : 'table-warning' }}">
                                            <td>{{ $startingNumber + $index }}</td>
                                            <td><span class="student-pen">{{ $row1['student_pen'] }}</span></td>
                                            <td>{{ $row1['student_name'] }}</td>
                                            <td>{{ $row1['father_name'] }}</td>
                                            <td>{{ $row1['gender'] == '1' ? 'Male' : 'Female' }}</td>
                                            <td>{{ date('d-m-Y', strtotime($row1['student_dob'])) }}</td>
                                            <td>{{ $row1['mobile_no_1'] }}</td>
                                            <td>{{ $row1['clsid'] }}</td>
                                            <td>{{ 'Section ' . chr(64 + $row1['section_id']) }}</td>

                                            <!-- Handle status and action based on current status -->
                                            @if(!in_array($row1['stud_status'], ['E', 'P']))
                                                <!-- Form for students who can be updated -->
                                                <form method="POST" action="{{ route('students.dropbox.update') }}" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="save" value="{{ $row1['student_pen'] }}">

                                                    <td>
                                                        <select class="form-select form-select-sm"
                                                                name="status[{{ $row1['student_pen'] }}]">
                                                            <option value="E" {{ $row1['stud_status'] == 'E' ? 'selected' : '' }}>Enrolled</option>
                                                            <option value="P" {{ $row1['stud_status'] == 'P' ? 'selected' : '' }}>Pending</option>
                                                        </select>
                                                    </td>

                                                    <td>
                                                        <button type="submit" class="btn btn-sm btn-success">
                                                            <i class="fas fa-save"></i> Save
                                                        </button>
                                                    </td>
                                                </form>
                                            @else
                                                <!-- Display-only for enrolled/pending students -->
                                                <td>
                                                    <span class="badge bg-{{ $row1['stud_status'] == 'E' ? 'success' : 'warning' }} text-white p-2">
                                                        {{ $row1['stud_status'] == 'E' ? 'Enrolled' : 'Pending' }}
                                                    </span>
                                                </td>

                                                <td>
                                                    <button type="button"
                                                    class="btn btn-sm {{ $row1['stud_status'] == 'E' ? 'btn-primary' : 'btn-warning' }}"
                                                    onclick="showStudentDetails({{ json_encode($row1) }})"
                                                    title="View student details">
                                                    <i class="fas fa-eye"></i> View
                                                    </button>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination Links -->
                            <div class="mt-4 d-flex justify-content-center">
                                {{ $rows->appends(request()->query())->links() }}
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i> No students found. Please adjust your filter criteria.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Details Modal -->
    <div class="modal fade" id="studentDetailsModal" tabindex="-1" aria-labelledby="studentDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="studentDetailsModalLabel">Student Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="alert" id="student-status-badge"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Student PEN</label>
                                <p id="modal-student-pen" class="form-control-plaintext"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Name</label>
                                <p id="modal-student-name" class="form-control-plaintext"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Father's Name</label>
                                <p id="modal-father-name" class="form-control-plaintext"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Gender</label>
                                <p id="modal-gender" class="form-control-plaintext"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Date of Birth</label>
                                <p id="modal-dob" class="form-control-plaintext"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Mobile Number</label>
                                <p id="modal-mobile" class="form-control-plaintext"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Class</label>
                                <p id="modal-class" class="form-control-plaintext"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Current Class</label>
                                <p id="modal-current-class" class="form-control-plaintext"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Current Section</label>
                                <p id="modal-section" class="form-control-plaintext"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Current School</label>
                                <p id="modal-school" class="form-control-plaintext"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .container-fluid {
                padding-left: 25px;
                padding-right: 25px;
                max-width: 100%;
            }

            [x-cloak] {
                display: none !important;
            }

            /* Table header sticky */
            .sticky-top {
                position: sticky;
                top: 0;
                z-index: 10;
            }

            /* Improve table height */
            .table-responsive {
                max-height: calc(100vh - 320px);
            }

            /* Notification styles */
            .notification-container {
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                min-width: 300px;
            }

            .notification {
                margin-bottom: 10px;
                padding: 15px;
                border-radius: 4px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                animation: slideIn 0.3s ease-out, fadeOut 0.5s ease-out 2.5s forwards;
            }

            /* Pagination styling */
            .pagination svg, nav svg {
                width: 14px !important;
                height: 14px !important;
                vertical-align: middle;
                max-width: 14px !important;
                max-height: 14px !important;
            }

            .pagination .page-item .page-link,
            nav .page-item .page-link {
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 0.375rem 0.75rem;
                font-size: 0.875rem;
                line-height: 1;
            }

            .pagination .page-item, nav .page-item {
                line-height: 1;
            }

            /* Loading spinner */
            .spinner-container {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 9999;
            }

            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }

            @keyframes fadeOut {
                from { opacity: 1; }
                to { opacity: 0; }
            }
        </style>
    @endpush

    <script>
        function showStudentDetails(student) {
            event.preventDefault();
            // Set the modal title
            document.getElementById('studentDetailsModalLabel').textContent =
                'Student Details: ' + student.student_name;

            // Populate student information
            document.getElementById('modal-student-pen').textContent = student.student_pen;
            document.getElementById('modal-student-name').textContent = student.student_name;
            document.getElementById('modal-father-name').textContent = student.father_name;
            document.getElementById('modal-gender').textContent = student.gender == '1' ? 'Male' : 'Female';

            // Format date nicely
            const dob = new Date(student.student_dob);
            const formattedDob = dob.toLocaleDateString('en-GB', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
            document.getElementById('modal-dob').textContent = formattedDob;

            document.getElementById('modal-mobile').textContent = student.mobile_no_1 || 'Not provided';
            document.getElementById('modal-class').textContent = student.clsid;

            // Create a mapping for class display
            const classNames = {
                '-3': 'Class Nursery/KG/PP3',
                '-2': 'Class LKG/KG1/PP2',
                '-1': 'Class UKG/KG2/PP1'
            };
            for (let i = 1; i <= 12; i++) {
                classNames[i] = 'Class ' + i;
            }

            document.getElementById('modal-current-class').textContent =
                classNames[student.pres_class] || 'Unknown';

            // Create a mapping for section display
            const sections = {
                '1': 'Section A',
                '2': 'Section B',
                '3': 'Section C',
                '4': 'Section D',
                '5': 'Section E',
                '6': 'Section F',
                '7': 'Section G'
            };

            document.getElementById('modal-section').textContent =
                sections[student.section_id] || 'Unknown';

            document.getElementById('modal-school').textContent = student.schname || 'Unknown';

            // Set status badge
            const statusBadge = document.getElementById('student-status-badge');
            if (student.stud_status === 'E') {
                statusBadge.className = 'alert alert-success';
                statusBadge.textContent = 'This student is currently Enrolled';
            } else if (student.stud_status === 'P') {
                statusBadge.className = 'alert alert-warning';
                statusBadge.textContent = 'This student is currently Pending';
            }

            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('studentDetailsModal'));
            modal.show();
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Make sure you have Bootstrap 5 JS included for the modal to work
            if (typeof bootstrap === 'undefined') {
                console.error('Bootstrap JS is not loaded. The modal may not work correctly.');
            }
        });

        function studentDropbox() {
            return {
                loading: false,

                updateRowStyle(element) {
                    const row = element.closest('tr');
                    const status = element.value;

                    // Remove existing classes
                    row.classList.remove('table-success', 'table-warning', 'table-danger');

                    // Add appropriate class based on status
                    if (status === 'E') {
                        row.classList.add('table-success');
                    } else if (status === 'P') {
                        row.classList.add('table-warning');
                    } else {
                        row.classList.add('table-danger');
                    }
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize page filter functionality
            const pageFilter = document.getElementById('pageFilter');
            const clearPageFilterBtn = document.getElementById('clearPageFilter');

            if (!pageFilter) return;

            pageFilter.addEventListener('input', function() {
                const filterTerm = this.value.toLowerCase().trim();

                // Find all student rows on the current page
                const rows = document.querySelectorAll('.student-row');

                // For each row, check if it matches the filter
                rows.forEach(row => {
                    // Get student name from input
                    const nameInput = row.querySelector('input[name^="sname"]');
                    const studentName = nameInput ? nameInput.value.toLowerCase() : '';

                    // Get student PEN from span
                    const penSpan = row.querySelector('.student-pen');
                    const studentPen = penSpan ? penSpan.textContent.toLowerCase() : '';

                    // Check if either contains the filter term
                    const matches = studentName.includes(filterTerm) || studentPen.includes(filterTerm);

                    // Show or hide based on match
                    row.style.display = matches || filterTerm === '' ? '' : 'none';
                });
            });

            // Set up clear button
            if (clearPageFilterBtn) {
                clearPageFilterBtn.addEventListener('click', function() {
                    pageFilter.value = '';
                    // Trigger the input event to show all rows
                    pageFilter.dispatchEvent(new Event('input'));
                });
            }
        });
    </script>
</x-FrontLayout>
