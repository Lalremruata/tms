<x-FrontLayout>
    <!-- Include Alpine.js and Font Awesome -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.12.0/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <div x-data="studentStatus()" class="student-status">
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
                            <h2 class="text-primary mb-0 fw-bold">Student Present Status</h2>
                        </div>
                        <div>
                            <a href="{{ route('vskdashboard') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <form id="statusForm" method="POST" action="{{ route('students.check-status') }}" class="row g-3" x-data="{ searchType: '{{ $searchType ?? 'pen' }}' }">
                        @csrf
                        <!-- Search Type Selection -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Search By</label>
                            <div class="d-flex">
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="radio" name="search_type" id="searchTypePen" value="pen"
                                           x-model="searchType" checked>
                                    <label class="form-check-label" for="searchTypePen">
                                        Student PEN
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="search_type" id="searchTypeName" value="name"
                                           x-model="searchType">
                                    <label class="form-check-label" for="searchTypeName">
                                        Student Name
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Search Term Input -->
                        <div class="col-md-6">
                            <label for="search_term" class="form-label fw-bold"
                                   x-text="searchType === 'pen' ? 'Student PEN' : 'Student Name'"></label>
                            <div class="input-group">
                                <span class="input-group-text" x-html="searchType === 'pen' ? '<i class=\'fas fa-id-card\'></i>' : '<i class=\'fas fa-user\'></i>'"></span>
                                <input type="text" class="form-control" id="search_term" name="search_term"
                                       x-bind:placeholder="searchType === 'pen' ? 'Enter Student PEN...' : 'Enter Student Name...'"
                                       value="{{ $searchTerm ?? '' }}" required>
                            </div>
                            <div class="form-text" x-show="searchType === 'name'">
                                <i class="fas fa-info-circle me-1"></i> Name search will return all matching students
                            </div>
                        </div>

                        <!-- Action buttons -->
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search me-1"></i> Search
                            </button>
                            <a href="{{ route('students.status') }}" class="btn btn-secondary">
                                <i class="fas fa-redo me-1"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Note -->
            <div class="alert alert-info mb-4">
                <i class="fas fa-info-circle me-2"></i> Search for a student by PEN or name to check their present status.
            </div>

            <!-- Results Section -->
            @if (isset($rows) && $rows->count() > 0)
                <div class="card shadow-sm">
                    <div class="card-body">
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

                        <div class="mb-3">
                            <p class="text-primary fw-bold">School: {{ $schname }} of the District: {{ $dstname }}</p>
                            <p>Found {{ $rows->count() }} student(s)</p>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-warning text-dark">
                                <tr>
                                    <th>Present Status</th>
                                    <th>Action to be Taken</th>
                                    <th>District</th>
                                    <th>UDISE Code</th>
                                    <th>School Name</th>
                                    <th>Student PEN</th>
                                    <th>Student Name</th>
                                    <th>Father's Name</th>
                                    <th>Gender</th>
                                    <th>Date of Birth</th>
                                    <th>Mobile Number</th>
                                    <th>Class</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($rows as $row)
                                    <tr>
                                        <td>
                                            <span class="badge bg-{{ $row->stud_status == 'E' ? 'success' : ($row->stud_status == 'P' ? 'warning' : 'danger') }} px-3 py-2">
                                                {{ $row->ststatus }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info text-dark px-3 py-2">{{ $row->trf }}</span>
                                        </td>
                                        <td>{{ $row->dstname }}</td>
                                        <td>{{ $row->udise_cd }}</td>
                                        <td>{{ $row->schname }}</td>
                                        <td>{{ $row->student_pen }}</td>
                                        <td>{{ $row->student_name }}</td>
                                        <td>{{ $row->father_name }}</td>
                                        <td>{{ $row->gender == '1' ? 'Male' : 'Female' }}</td>
                                        <td>{{ $row->formatted_dob ?? ($row->student_dob ? date('d/m/Y', strtotime($row->student_dob)) : 'N/A') }}</td>
                                        <td>{{ $row->mobile_no_1 }}</td>
                                        <td>{{ $row->clsid }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Action Buttons Based on Status - Only for single student view -->
                        @if($rows->count() == 1)
                            @php
                                $status = $rows[0]->stud_status ?? '';
                                $actionRoute = '';
                                $actionText = '';
                                $actionIcon = '';
                                $actionClass = '';

                                if (in_array($status, ['W', 'T', 'A', 'O'])) {
                                    $actionRoute = 'student-dropbox';
                                    $actionText = 'Go to Dropbox Facility';
                                    $actionIcon = 'inbox';
                                    $actionClass = 'btn-success';
                                } elseif ($status == 'P') {
                                    $actionRoute = 'students.transfers';  // You'll need to create this route
                                    $actionText = 'Go to Transfer Facility';
                                    $actionIcon = 'exchange-alt';
                                    $actionClass = 'btn-warning';
                                }
                            @endphp

                            @if($actionRoute)
                                <div class="d-flex justify-content-end mt-3">
                                    <a href="{{ route($actionRoute) }}" class="btn {{ $actionClass }}">
                                        <i class="fas fa-{{ $actionIcon }} me-2"></i> {{ $actionText }}
                                    </a>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            @elseif(isset($rows))
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i> No student found with the provided search term. Please check and try again.
                </div>
            @endif
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

            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }

            @keyframes fadeOut {
                from { opacity: 1; }
                to { opacity: 0; }
            }

            /* Make the table not too wide */
            .table-responsive {
                overflow-x: auto;
            }

            /* Status badges */
            .badge {
                font-size: 0.85rem;
                font-weight: 500;
            }
        </style>
    @endpush

    <script>
        function studentStatus() {
            return {
                loading: false,

                init() {
                    // Focus on the search input field when page loads
                    document.getElementById('search_term').focus();

                    // Add loading state to form submission
                    const form = document.getElementById('statusForm');
                    if (form) {
                        form.addEventListener('submit', () => {
                            this.loading = true;
                        });
                    }
                }
            }
        }
    </script>
</x-FrontLayout>
