<!-- resources/views/students/list.blade.php -->
<x-FrontLayout>
    <div class="student-list-container">
        <!-- Notification System -->
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

        <!-- Page Header with Breadcrumbs -->
        <div class="header-section mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="app-page-title">Student List</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('vskdashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Students</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('vskdashboard') }}" class="btn btn-outline-primary me-2">
                        <i class="fas fa-home me-2"></i>Dashboard
                    </a>
                    <a href="{{ route('students.add') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add Student
                    </a>
                </div>
            </div>
        </div>

        @if($schoolInfo)
            <!-- School Information Card -->
            <div class="card mb-4 shadow-sm school-info-card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-school me-2"></i>School Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label class="text-muted small d-block">District</label>
                            <span class="fw-bold fs-5">{{ $schoolInfo->district_name }}</span>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label class="text-muted small d-block">Block</label>
                            <span class="fw-bold fs-5">{{ $schoolInfo->block_name }}</span>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label class="text-muted small d-block">UDISE Code</label>
                            <span class="fw-bold fs-5">{{ $schoolInfo->udise_sch_code }}</span>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small d-block">School Name</label>
                            <span class="fw-bold fs-5">{{ $schoolInfo->school_name }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i> No school information found for your account. Please contact the administrator.
            </div>
        @endif

        <!-- Student List Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <a href="{{ route('students.export')}}"
                   class="btn btn-sm btn-success">
                    <i class="ri-file-excel-line me-1"></i> Export to Excel
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover student-table mb-0">
                        <thead>
                            <tr>
                                <th class="text-center">Student PEN</th>
                                <th class="text-center">Apaar id</th>
                                <th>Student Name</th>
                                <th class="text-center">Gender</th>
                                <th class="text-center">Date of Birth</th>
                                <th>Father Name</th>
                                <th>Mother Name</th>
                                <th class="text-center">Mobile No.</th>
                                <th class="text-center">Class</th>
                                <th class="text-center">Section</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                                <tr>
                                    <td class="text-center">{{ $student->student_pen ?? '-' }}</td>
                                    <td class="text-center">{{ $student->apaar_id ?? '-' }}</td>
                                    <td>{{ $student->student_name }}</td>
                                    <td class="text-center">
                                        @if($student->gender == 'Male')
                                            <span class="badge rounded-pill bg-info text-dark">
                                                <i class="fas fa-mars me-1"></i>Male
                                            </span>
                                        @elseif($student->gender == 'Female')
                                            <span class="badge rounded-pill bg-danger">
                                                <i class="fas fa-venus me-1"></i>Female
                                            </span>
                                        @else
                                            {{ $student->gender }}
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $student->student_dob }}</td>
                                    <td>{{ $student->father_name }}</td>
                                    <td>{{ $student->mother_name }}</td>
                                    <td class="text-center">{{ $student->mobile_no_1 }}</td>
                                    <td class="text-center">{{ $student->clsid }}</td>
                                    <td class="text-center">{{ $student->section_name ?? $student->section_id }}</td>
                                    <td class="text-center">
                                        @if($student->stud_status == 'Enrolled' || $student->stud_Status == 'Enrolled')
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i>Enrolled
                                            </span>
                                        @elseif($student->stud_status == 'Pending' || $student->stud_Status == 'Pending')
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-clock me-1"></i>Pending
                                            </span>
                                        @else
                                            {{ $student->stud_status ?? $student->stud_Status }}
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                            <h5>No Students Found</h5>
                                            <p class="text-muted">No student records available at the moment.</p>
                                            <a href="{{ route('students.add') }}" class="btn btn-primary mt-2">
                                                <i class="fas fa-plus me-2"></i>Add First Student
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                            <!-- Pagination Links -->
            <div class="mt-4 d-flex justify-content-center">
                {{ $students->links() }}
            </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        /* Main container styles */
        .student-list-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        /* Page header styles */
        .app-page-title {
            color: #3a0603;
            font-weight: 700;
            margin-bottom: 0.25rem;
            font-size: 1.75rem;
        }

        .breadcrumb {
            font-size: 0.85rem;
        }

        /* Card styling */
        .card {
            border: none;
            border-radius: 0.75rem;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            font-weight: 600;
            padding: 0.75rem 1.25rem;
        }

        .school-info-card {
            background-color: #fff;
        }

        .school-info-card .card-header {
            background-color: #732bf5;
        }

        /* Table styling */
        .student-table {
            margin-bottom: 0;
        }

        .student-table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            padding: 1rem 0.75rem;
            vertical-align: middle;
            white-space: nowrap;
        }

        .student-table tbody td {
            padding: 0.75rem;
            vertical-align: middle;
        }

        .student-table tbody tr:hover {
            background-color: rgba(115, 43, 245, 0.05);
        }

        /* Badge styling */
        .badge {
            font-weight: 500;
            padding: 0.5em 0.8em;
        }

        /* Button styling */
        .btn {
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #732bf5;
            border-color: #732bf5;
        }

        .btn-primary:hover, .btn-primary:focus {
            background-color: #5a1fcc;
            border-color: #5a1fcc;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        /* Empty state styling */
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        /* Notification system */
        .notification-container {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1050;
            max-width: 400px;
        }

        .alert {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border-radius: 0.5rem;
            border-left: 4px solid;
        }

        .alert-success {
            border-left-color: #198754;
        }

        .alert-danger {
            border-left-color: #dc3545;
        }

                /* Pagination styling */
                .pagination {
            margin-bottom: 0;
        }

        .pagination .page-item .page-link {
            border-radius: 0.5rem;
            margin: 0 0.125rem;
            color: #732bf5;
            transition: all 0.2s ease;
        }

        .pagination .page-item.active .page-link {
            background-color: #732bf5;
            border-color: #732bf5;
            color: white;
        }

        .pagination .page-item .page-link:hover {
            background-color: rgba(115, 43, 245, 0.1);
        }

        .pagination .page-item.disabled .page-link {
            color: #6c757d;
            pointer-events: none;
            background-color: #fff;
            border-color: #dee2e6;
        }

        .card-footer {
            background-color: #f8f9fa;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            padding: 0.75rem 1.25rem;
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .card-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @media (max-width: 768px) {
            .app-page-title {
                font-size: 1.5rem;
            }

            .btn-lg {
                padding: 0.5rem 1rem;
                font-size: 1rem;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Auto-dismiss alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });
    </script>
    @endpush
</x-FrontLayout>
