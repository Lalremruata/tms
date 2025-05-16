<x-FrontLayout>
    <!-- Include Alpine.js directly -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.12.0/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <div x-data="studentCorrection()" class="student-correction">
        <!-- Loading Spinner -->
        <div class="spinner-container" x-show="loading" x-cloak>
            <div class="spinner-border text-light" style="width: 3rem; height: 3rem;" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <!-- Alert container for flash messages -->
        <div id="alertContainer" class="notification-container">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
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
                            <h2 class="text-primary mb-0 fw-bold">Student Data Correction</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <form id="filterForm" method="GET" action="{{ route('students.correction') }}" class="row g-3">
                        <!-- Search field -->
                        <div class="col-md-3">
                            <label for="search" class="form-label fw-bold">Search Students</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="search" name="search" 
                                       placeholder="Name or PEN..." 
                                       value="{{ $searchTerm ?? '' }}">
                            </div>
                        </div>
                        
                        <!-- Class dropdown -->
                        <div class="col-md-3">
                            <label for="parameter2" class="form-label fw-bold">Class</label>
                            <select name="parameter2" id="parameter2" class="form-select">
                                <option value="">All Classes</option>
                                @foreach ($classOptions as $option)
                                    <option value="{{ $option->pres_class }}" {{ (isset($selectedClass) && $selectedClass == $option->pres_class) ? 'selected' : '' }}>
                                        {{ $option->cls }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
            
                        <!-- Section dropdown -->
                        <div class="col-md-3">
                            <label for="sec" class="form-label fw-bold">Section</label>
                            <select name="sec" id="sec" class="form-select">
                                <option value="0">All Sections</option>
                                @foreach ($sectionOptions as $option)
                                    @if($option->section_id != 0)
                                        <option value="{{ $option->section_id }}" {{ (isset($selectedSection) && $selectedSection == $option->section_id) ? 'selected' : '' }}>
                                            {{ $option->sec }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
            
                        <!-- Action buttons -->
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-1"></i> Apply
                                </button>
            
                                <a href="{{ route('students.correction') }}" class="btn btn-secondary">
                                    <i class="fas fa-redo"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Data Table Section -->
            @if (isset($students) && $students->count() > 0)
                <div class="card shadow-sm">
                    <div class="card-body">
                        <!-- Pagination Summary -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <span class="text-muted">Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }} students</span>
                                @if($searchTerm)
                                    <span class="badge bg-info ms-2">Search: "{{ $searchTerm }}"</span>
                                @endif
                                @if($selectedClass)
                                    <span class="badge bg-primary ms-2">
                                        Class: {{ collect($classOptions)->firstWhere('pres_class', $selectedClass)->cls ?? '' }}
                                    </span>
                                @endif
                                @if($selectedSection && $selectedSection != '0')
                                    <span class="badge bg-secondary ms-2">
                                        Section: {{ collect($sectionOptions)->firstWhere('section_id', (int)$selectedSection)->sec ?? '' }}
                                    </span>
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

                        <!-- Top scroll container -->
                        <div class="top-scroll-container">
                            <div class="top-scroll-content"></div>
                        </div>

                        <div class="table-responsive table-scroll-container">
                            <table class="table table-bordered table-hover">
                                <thead class="table-primary sticky-top">
                                <tr>
                                    <th>Sl.No.</th>
                                    <th>Student PEN</th>
                                    <th>Student Name</th>
                                    <th>Status</th>
                                    <th>Category</th>
                                    <th>Aadhaar Number</th>
                                    <th>Apaar ID</th>
                                    <th>Nationality</th>
                                    <th>Remark</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    // Calculate serial number based on pagination
                                    $slno = ($students->currentPage() - 1) * $students->perPage();
                                @endphp
                                @foreach ($students as $row)
                                @php $slno = $slno + 1 @endphp
                                <tr id="row-{{ $row->student_pen }}" class="student-row {{ (strpos($row->stud_status, 'E') !== false) ? 'table-success' : ((strpos($row->stud_status, 'P') !== false) ? 'table-warning' : 'table-danger') }}">
                                    <form action="{{ route('students.update-inline') }}" method="POST" class="student-update-form">
                                        @csrf
                                        <input type="hidden" name="student_pen" value="{{ $row->student_pen }}">
                                        
                                        <td>{{ $slno }}</td>
                                        <td>
                                            <span class="student-pen">{{ $row->student_pen }}</span>
                                        </td>
                                        <td>
                                            <input class="form-control form-control-sm wide-field" type="text" 
                                                   name="student_name" value="{{ $row->student_name }}" required>
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm narrow-field status-select"
                                                    name="status" required
                                                    x-on:change="updateRowStyle($event.target)">
                                                <option value="E" {{ $row->stud_status == 'E' ? 'selected' : '' }}>Enrolled</option>
                                                <option value="W" {{ $row->stud_status == 'W' ? 'selected' : '' }}>Wrong Entry</option>
                                                <option value="T" {{ $row->stud_status == 'T' ? 'selected' : '' }}>Taken TC</option>
                                                <option value="A" {{ $row->stud_status == 'A' ? 'selected' : '' }}>Long Absentees</option>
                                                <option value="D" {{ $row->stud_status == 'D' ? 'selected' : '' }}>Demised</option>
                                                <option value="P" {{ $row->stud_status == 'P' ? 'selected' : '' }}>Pending</option>
                                                <option value="O" {{ $row->stud_status == 'O' ? 'selected' : '' }}>Passed Out</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm narrow-field"
                                                    name="category" required>
                                                <option value="1" {{ $row->soc_cat_id == '1' ? 'selected' : '' }}>SC</option>
                                                <option value="2" {{ $row->soc_cat_id == '2' ? 'selected' : '' }}>ST</option>
                                                <option value="3" {{ $row->soc_cat_id == '3' ? 'selected' : '' }}>OBC</option>
                                                <option value="4" {{ $row->soc_cat_id == '4' ? 'selected' : '' }}>General</option>
                                                <option value="5" {{ $row->soc_cat_id == '5' ? 'selected' : '' }}>Others</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input class="form-control form-control-sm medium-field" type="text" maxlength="12"
                                                   name="aadhaar" value="{{ $row->aadhaar_no ?? '' }}"
                                                   oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 12)">
                                        </td>
                                        <td>
                                            <input class="form-control form-control-sm medium-field" type="text"
                                                   name="apaar" value="{{ $row->apaar_id ?? '' }}">
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm narrow-field"
                                                    name="nationality">
                                                <option value="Indian" {{ ($row->nationality == 'Indian' || empty($row->nationality)) ? 'selected' : '' }}>Indian</option>
                                                <option value="Foreigner" {{ ($row->nationality == 'Foreigner') ? 'selected' : '' }}>Foreigner</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input class="form-control form-control-sm medium-field" type="text"
                                                   name="remark" value="{{ $row->remark ?? '' }}">
                                        </td>
                                        <td>
                                            <div class="d-flex flex-row gap-2">
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="fas fa-save"></i> Save
                                                </button>
                                                <a href="{{ route('students.edit', $row->student_pen) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </div>
                                        </td>
                                    </form>
                                </tr>
                            @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination Links -->
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $students->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> No students found. Please adjust your filter criteria.
                </div>
            @endif
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/student-correction.css') }}">
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
            max-width: 14px !important; /* Add this to prevent inheritance issues */
            max-height: 14px !important; /* Add this to prevent inheritance issues */
        }

        /* Ensure the container doesn't force the SVGs larger */
        .pagination .page-item .page-link, 
        nav .page-item .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            line-height: 1;
        }

        /* Additional insurance for proper sizing */
        .pagination .page-item, nav .page-item {
            line-height: 1;
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
                    const nameInput = row.querySelector('input[name="student_name"]');
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