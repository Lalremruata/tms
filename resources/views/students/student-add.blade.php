<!-- resources/views/students/student-add.blade.php -->
<x-FrontLayout>
    <div class="student-add-container">
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
                    <h1 class="app-page-title">Student Addition</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('vskdashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Add New Student</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('vskdashboard') }}" class="btn btn-outline-primary">
                        <i class="fas fa-home me-2"></i>Dashboard
                    </a>
                </div>
            </div>
        </div>

        @if(isset($schoolInfo))
            <!-- School Information Card -->
            <div class="card mb-4 shadow-sm school-info-card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-school me-2"></i>School Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label class="text-muted small d-block">District</label>
                            <span class="fw-bold fs-5">{{ $schoolInfo->dstname }}</span>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label class="text-muted small d-block">Block</label>
                            <span class="fw-bold fs-5">{{ $schoolInfo->blkname }}</span>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label class="text-muted small d-block">UDISE Code</label>
                            <span class="fw-bold fs-5">{{ $schoolInfo->ucode }}</span>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small d-block">School Name</label>
                            <span class="fw-bold fs-5">{{ $schoolInfo->schnm }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student Addition Form -->
            <form action="{{ route('students.store') }}" method="POST" id="studentAddForm" novalidate class="needs-validation">
                @csrf

                <!-- Hidden fields for school information -->
                <input type="hidden" name="dstcd" value="{{ $schoolInfo->dstcd }}">
                <input type="hidden" name="blkcd" value="{{ $schoolInfo->blkcd }}">
                <input type="hidden" name="schnm" value="{{ $schoolInfo->schnm }}">
                <input type="hidden" name="ucode" value="{{ $schoolInfo->ucode }}">
                <input type="hidden" name="schcat" value="{{ $schoolInfo->schcat }}">
                <input type="hidden" name="schtyp" value="{{ $schoolInfo->schtyp }}">
                <input type="hidden" name="schmgt" value="{{ $schoolInfo->schmgmt }}">

                <div class="row g-4">
                    <!-- Basic Information Card -->
                    <div class="col-12">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Basic Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" name="nattchcode" id="studentpen" class="form-control @error('nattchcode') is-invalid @enderror"
                                                  value="{{ old('nattchcode') }}" placeholder="Student PEN">
                                            <label for="studentpen">Student PEN (Optional)</label>
                                            <div class="form-text">Leave blank to auto-generate</div>
                                            @error('nattchcode')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" name="tchname" id="studentname" class="form-control @error('tchname') is-invalid @enderror"
                                                  value="{{ old('tchname') }}" placeholder="Student Name" required>
                                            <label for="studentname">Student Name <span class="text-danger">*</span></label>
                                            @error('tchname')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <select name="se_x" id="se_x" class="form-select @error('se_x') is-invalid @enderror" required>
                                                <option value="" disabled selected>Select gender</option>
                                                <option value="1" {{ old('se_x') == '1' ? 'selected' : '' }}>Male</option>
                                                <option value="2" {{ old('se_x') == '2' ? 'selected' : '' }}>Female</option>
                                            </select>
                                            <label for="se_x">Gender <span class="text-danger">*</span></label>
                                            @error('se_x')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3 mt-2">
                                    <div class="col-md-4">
                                        <div class="form-floating date-picker-container">
                                            <input type="text" id="dateraw" name="dob_raw" class="form-control @error('dob') is-invalid @enderror"
                                                  value="{{ old('dob_raw') }}" placeholder="DD/MM/YYYY" required>
                                            <label for="dateraw">Date of Birth <span class="text-danger">*</span></label>
                                            <input type="hidden" id="date" name="dob" value="{{ old('dob') }}">
                                            <div class="form-text">Format: DD/MM/YYYY (e.g., 15/08/2015)</div>
                                            @error('dob')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <select name="prcls" id="studentclass" class="form-select @error('prcls') is-invalid @enderror" required>
                                                <option value="" disabled selected>Select class</option>
                                                @foreach ($classOptions as $option)
                                                    <option value="{{ $option->clsid }}" {{ old('prcls') == $option->clsid ? 'selected' : '' }}>
                                                        {{ $option->cls_desc }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <label for="studentclass">Class <span class="text-danger">*</span></label>
                                            @error('prcls')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <select name="sec" id="studentsec" class="form-select @error('sec') is-invalid @enderror" required>
                                                <option value="" disabled selected>Select section</option>
                                                <option value="1" {{ old('sec') == '1' ? 'selected' : '' }}>Section A</option>
                                                <option value="2" {{ old('sec') == '2' ? 'selected' : '' }}>Section B</option>
                                                <option value="3" {{ old('sec') == '3' ? 'selected' : '' }}>Section C</option>
                                                <option value="4" {{ old('sec') == '4' ? 'selected' : '' }}>Section D</option>
                                                <option value="5" {{ old('sec') == '5' ? 'selected' : '' }}>Section E</option>
                                                <option value="6" {{ old('sec') == '6' ? 'selected' : '' }}>Section F</option>
                                                <option value="7" {{ old('sec') == '7' ? 'selected' : '' }}>Section G</option>
                                                <option value="8" {{ old('sec') == '8' ? 'selected' : '' }}>Section H</option>
                                            </select>
                                            <label for="studentsec">Section <span class="text-danger">*</span></label>
                                            @error('sec')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Family Information Card -->
                    <div class="col-md-6">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fas fa-users me-2"></i>Family Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="studentfather" class="form-label">Father's Name <span class="text-danger">*</span></label>
                                    <input type="text" name="fthname" id="studentfather"
                                           class="form-control @error('fthname') is-invalid @enderror"
                                           value="{{ old('fthname') }}" required>
                                    @error('fthname')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="studentmother" class="form-label">Mother's Name <span class="text-danger">*</span></label>
                                    <input type="text" name="mthname" id="studentmother"
                                           class="form-control @error('mthname') is-invalid @enderror"
                                           value="{{ old('mthname') }}" required>
                                    @error('mthname')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="studentguardian" class="form-label">Guardian's Name</label>
                                    <input type="text" name="guardname" id="studentguardian"
                                           class="form-control @error('guardname') is-invalid @enderror"
                                           value="{{ old('guardname') }}">
                                    @error('guardname')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information Card -->
                    <div class="col-md-6">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fas fa-address-book me-2"></i>Contact Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="studentaddress" class="form-label">Address <span class="text-danger">*</span></label>
                                    <textarea name="addr" id="studentaddress" rows="2"
                                              class="form-control @error('addr') is-invalid @enderror"
                                              required>{{ old('addr') }}</textarea>
                                    @error('addr')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="studentpincode" class="form-label">Pincode</label>
                                        <input type="text" name="pcode" id="studentpincode" maxlength="6"
                                               class="form-control @error('pcode') is-invalid @enderror"
                                               value="{{ old('pcode') }}">
                                        @error('pcode')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label for="studentmobile" class="form-label">Mobile No.</label>
                                        <input type="tel" name="mob" id="studentmobile" maxlength="10"
                                               class="form-control @error('mob') is-invalid @enderror"
                                               value="{{ old('mob') }}">
                                        @error('mob')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label for="studentemail" class="form-label">Email</label>
                                        <input type="email" name="eml" id="studentemail"
                                               class="form-control @error('eml') is-invalid @enderror"
                                               value="{{ old('eml') }}">
                                        @error('eml')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information Card -->
                    <div class="col-12">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Additional Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label for="cat" class="form-label">Social Category <span class="text-danger">*</span></label>
                                        <select name="cat" id="cat" class="form-select @error('cat') is-invalid @enderror" required>
                                            <option value="" disabled selected>Select category</option>
                                            <option value="1" {{ old('cat') == '1' ? 'selected' : '' }}>SC</option>
                                            <option value="2" {{ old('cat') == '2' ? 'selected' : '' }}>ST</option>
                                            <option value="3" {{ old('cat') == '3' ? 'selected' : '' }}>OBC</option>
                                            <option value="4" {{ old('cat') == '4' ? 'selected' : '' }}>General</option>
                                        </select>
                                        @error('cat')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="cws" class="form-label">Is CWSN Child <span class="text-danger">*</span></label>
                                        <select name="cws" id="cws" class="form-select @error('cws') is-invalid @enderror" required>
                                            <option value="" disabled selected>Select option</option>
                                            <option value="2" {{ old('cws') == '2' ? 'selected' : '' }}>No</option>
                                            <option value="1" {{ old('cws') == '1' ? 'selected' : '' }}>Yes</option>
                                        </select>
                                        @error('cws')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="nationality" class="form-label">Nationality <span class="text-danger">*</span></label>
                                        <select name="nationality" id="nationality" class="form-select @error('nationality') is-invalid @enderror" required>
                                            <option value="" disabled selected>Select nationality</option>
                                            <option value="Indian" {{ old('nationality') == 'Indian' ? 'selected' : '' }}>Indian</option>
                                            <option value="Foreigner" {{ old('nationality') == 'Foreigner' ? 'selected' : '' }}>Foreigner</option>
                                        </select>
                                        @error('nationality')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="remark" class="form-label">Remarks</label>
                                        <input type="text" name="remark" id="remark"
                                               class="form-control @error('remark') is-invalid @enderror"
                                               value="{{ old('remark') }}">
                                        @error('remark')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row g-3 mt-3">
                                    <div class="col-md-6">
                                        <label for="aadhaar" class="form-label">
                                            Aadhaar Number
                                            <i class="fas fa-info-circle" data-bs-toggle="tooltip"
                                               title="12-digit number without spaces"></i>
                                        </label>
                                        <input type="text" name="aadhaar" id="aadhaar"
                                               class="form-control @error('aadhaar') is-invalid @enderror"
                                               value="{{ old('aadhaar') }}" maxlength="12">
                                        @error('aadhaar')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="apaar" class="form-label">Apaar ID</label>
                                        <input type="text" name="apaar" id="apaar"
                                               class="form-control @error('apaar') is-invalid @enderror"
                                               value="{{ old('apaar') }}">
                                        @error('apaar')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="d-flex justify-content-between mb-5">
                    <a href="{{ route('students.index') }}" class="btn btn-light btn-lg px-5">
                        <i class="fas fa-arrow-left me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="fas fa-save me-2"></i>Save Student
                    </button>
                </div>
            </form>
        @else
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i> Teacher information not found or you don't have permission to add students.
            </div>
        @endif
    </div>

    @push('styles')
    <style>
        /* Main container styles */
        .student-add-container {
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

        /* Form inputs */
        .form-control, .form-select {
            border-radius: 0.5rem;
            border: 1px solid #ddd;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #732bf5;
            box-shadow: 0 0 0 0.25rem rgba(115, 43, 245, 0.25);
        }

        .form-control.is-invalid, .form-select.is-invalid {
            border-color: #dc3545;
            background-image: none;
        }

        .form-floating>.form-control,
        .form-floating>.form-select {
            height: calc(3.5rem + 2px);
            padding: 1rem 0.75rem;
        }

        .form-floating>label {
            padding: 1rem 0.75rem;
        }

        /* Form feedback */
        .form-text {
            font-size: 0.75rem;
            color: #6c757d;
        }

        .invalid-feedback {
            font-weight: 500;
        }

        /* Required field marker */
        .text-danger {
            color: #dc3545;
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

        /* Buttons */
        .btn {
            border-radius: 0.5rem;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
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

        /* Date picker styling */
        .date-picker-container {
            position: relative;
        }

        /* Responsive adjustments */
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

            // Date of birth formatter
            const dateRawInput = document.getElementById('dateraw');
            const dateInput = document.getElementById('date');

            if (dateRawInput && dateInput) {
                // Initialize with any existing value
                if (dateRawInput.value) {
                    formatDate();
                }

                dateRawInput.addEventListener('input', formatDate);

                function formatDate() {
                    const rawDate = dateRawInput.value;
                    const datePattern = /^(\d{2})\/(\d{2})\/(\d{4})$/;  // Regex to match dd/mm/yyyy format

                    // Check if the input matches the required pattern
                    if (datePattern.test(rawDate)) {
                        // Extract day, month, and year from the rawDate
                        const [ , day, month, year] = rawDate.match(datePattern);

                        // Convert to dd-mm-yyyy format
                        const formattedDate = `${day}-${month}-${year}`;

                        // Set the formatted date in the hidden field
                        dateInput.value = formattedDate;

                        // Remove the 'invalid' state if the pattern is correct
                        dateRawInput.setCustomValidity('');
                        dateRawInput.classList.remove('is-invalid');
                        dateRawInput.classList.add('is-valid');
                    } else {
                        // If the pattern is incorrect, set a custom validation message
                        dateRawInput.setCustomValidity('Please enter the date in the format dd/mm/yyyy.');
                        dateRawInput.classList.add('is-invalid');
                    }
                }
            }

            // Numeric input validation
            const numericInputs = document.querySelectorAll('#aadhaar, #studentmobile, #studentpincode');
            numericInputs.forEach(input => {
                input.addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            });

            // Form validation enhancement
            const form = document.getElementById('studentAddForm');
            if (form) {
                form.addEventListener('submit', function(event) {
                    if (!this.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();

                        // Focus the first invalid field
                        const firstInvalidField = document.querySelector('.form-control:invalid, .form-select:invalid');
                        if (firstInvalidField) {
                            firstInvalidField.focus();

                            // Scroll to the first invalid field
                            const fieldBox = firstInvalidField.getBoundingClientRect();
                            const offset = window.pageYOffset + fieldBox.top - 100;
                            window.scrollTo({ top: offset, behavior: 'smooth' });
                        }
                    }

                    this.classList.add('was-validated');
                });
            }

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
