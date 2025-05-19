<x-FrontLayout>
    <!-- Include Alpine.js directly -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.12.0/dist/cdn.min.js"></script>

    <div x-data="studentEdit()" class="student-edit">
        <!-- Loading Spinner -->
        <div class="spinner-container" x-show="loading" x-cloak>
            <div class="spinner-border text-light" style="width: 3rem; height: 3rem;" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <!-- Alert container for notifications -->
        <div id="alertContainer" class="notification-container"></div>

        <div class="container-fluid px-4">
            <!-- Header Section -->
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <h2 class="text-primary mb-0 fw-bold">Edit Student Data</h2>
                        </div>
                        <div>
                            <a href="{{ route('students.correction') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Form Section -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('students.update') }}" method="POST" id="editStudentForm" class="row g-3">
                        @csrf
{{--                        <input type="hidden" id="student_pen" name="student_pen" value="{{ $student->student_pen }}">--}}

                        <!-- Student Information Section -->
                        <div class="col-md-6">
                            <label for="student_pen" class="form-label fw-bold">Current Student PEN</label>
                            <input type="text" class="form-control" id="student_pen" name="student_pen" value="{{ $student->student_pen }}" readonly>
                        </div>

                        <div class="col-md-6">
                            <label for="new_student_pen" class="form-label fw-bold">New Student PEN (only if changing)</label>
                            <input type="text" class="form-control" id="new_student_pen" name="new_student_pen" placeholder="Leave blank to keep current PEN">
                        </div>
                        <div class="col-12 mb-3">
                            <h5 class="text-primary border-bottom pb-2">Student Information</h5>
                        </div>

                        <div class="col-md-6">
                            <label for="student_name" class="form-label fw-bold">Student Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="student_name" name="student_name" value="{{ $student->student_name }}" required>
                        </div>

                        <div class="col-md-6">
                            <label for="father_name" class="form-label fw-bold">Father's Name</label>
                            <input type="text" class="form-control" id="father_name" name="father_name" value="{{ $student->father_name }}">
                        </div>

                        <div class="col-md-6">
                            <label for="mother_name" class="form-label fw-bold">Mother's Name</label>
                            <input type="text" class="form-control" id="mother_name" name="mother_name" value="{{ $student->mother_name }}">
                        </div>

                        <div class="col-md-3">
                            <label for="gender" class="form-label fw-bold">Gender <span class="text-danger">*</span></label>
                            <select class="form-select" id="gender" name="gender" required>
                                <option value="1" {{ $student->gender == '1' ? 'selected' : '' }}>Male</option>
                                <option value="2" {{ $student->gender == '2' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="dob" class="form-label fw-bold">Date of Birth <span class="text-danger">*</span></label>
                            @php $dob = $student->student_dob ? date('Y-m-d', strtotime($student->student_dob)) : ''; @endphp
                            <input type="date" class="form-control" id="dob" name="dob" value="{{ $dob }}" required>
                        </div>

                        <div class="col-md-4">
                            <label for="mobile" class="form-label fw-bold">Mobile Number</label>
                            <input type="tel" class="form-control" id="mobile" name="mobile" maxlength="10" value="{{ $student->mobile_no_1 }}">
                        </div>

                        <!-- Academic Information Section -->
                        <div class="col-12 mb-3 mt-4">
                            <h5 class="text-primary border-bottom pb-2">Academic Information</h5>
                        </div>

                        <div class="col-md-4">
                            <label for="class" class="form-label fw-bold">Current Class <span class="text-danger">*</span></label>
                            <select class="form-select" id="class" name="class" required>
                                @foreach ($classOptions as $option)
                                    <option value="{{ $option->pres_class }}" {{ $student->pres_class == $option->pres_class ? 'selected' : '' }}>
                                        {{ $option->cls }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="section" class="form-label fw-bold">Section <span class="text-danger">*</span></label>
                            <select class="form-select" id="section" name="section" required>
                                @foreach ($sectionOptions as $option)
                                    @if ($option->section_id != 0) {{-- Skip "All Sections" option --}}
                                    <option value="{{ $option->section_id }}" {{ $student->section_id == $option->section_id ? 'selected' : '' }}>
                                        {{ $option->sec }}
                                    </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="status" class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="E" {{ $student->stud_status == 'E' ? 'selected' : '' }}>Enrolled</option>
                                <option value="W" {{ $student->stud_status == 'W' ? 'selected' : '' }}>Wrong Entry</option>
                                <option value="T" {{ $student->stud_status == 'T' ? 'selected' : '' }}>Taken TC</option>
                                <option value="A" {{ $student->stud_status == 'A' ? 'selected' : '' }}>Long Absentees</option>
                                <option value="D" {{ $student->stud_status == 'D' ? 'selected' : '' }}>Demised</option>
                                <option value="P" {{ $student->stud_status == 'P' ? 'selected' : '' }}>Pending</option>
                                <option value="O" {{ $student->stud_status == 'O' ? 'selected' : '' }}>Passed Out</option>
                            </select>
                        </div>

                        <!-- Other Information Section -->
                        <div class="col-12 mb-3 mt-4">
                            <h5 class="text-primary border-bottom pb-2">Other Information</h5>
                        </div>

                        <div class="col-md-4">
                            <label for="category" class="form-label fw-bold">Social Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="1" {{ $student->soc_cat_id == '1' ? 'selected' : '' }}>SC</option>
                                <option value="2" {{ $student->soc_cat_id == '2' ? 'selected' : '' }}>ST</option>
                                <option value="3" {{ $student->soc_cat_id == '3' ? 'selected' : '' }}>OBC</option>
                                <option value="4" {{ $student->soc_cat_id == '4' ? 'selected' : '' }}>General</option>
                                <option value="5" {{ $student->soc_cat_id == '5' ? 'selected' : '' }}>Others</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="cwsn" class="form-label fw-bold">CWSN Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="cwsn" name="cwsn" required>
                                <option value="1" {{ $student->cwsn_yn == '1' ? 'selected' : '' }}>Yes</option>
                                <option value="2" {{ $student->cwsn_yn == '2' ? 'selected' : '' }}>No</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="nationality" class="form-label fw-bold">Nationality</label>
                            <select class="form-select" id="nationality" name="nationality">
                                <option value="Indian" {{ ($student->nationality == 'Indian' || empty($student->nationality)) ? 'selected' : '' }}>Indian</option>
                                <option value="Foreigner" {{ ($student->nationality == 'Foreigner') ? 'selected' : '' }}>Foreigner</option>
                            </select>
                        </div>

                        <!-- ID Information Section -->
                        <div class="col-md-6">
                            <label for="aadhaar" class="form-label fw-bold">Aadhaar Number</label>
                            <input type="text" class="form-control" id="aadhaar" name="aadhaar" maxlength="12" value="{{ $student->aadhaar_no ?? '' }}">
                            <div class="form-text">12-digit number, numeric only</div>
                        </div>

                        <div class="col-md-6">
                            <label for="apaar" class="form-label fw-bold">Apaar ID</label>
                            <input type="text" class="form-control" id="apaar" name="apaar" value="{{ $student->apaar_id ?? '' }}">
                        </div>

                        <div class="col-12">
                            <label for="remark" class="form-label fw-bold">Remarks</label>
                            <textarea class="form-control" id="remark" name="remark" rows="2">{{ $student->remark ?? '' }}</textarea>
                        </div>

                        <!-- Form Actions -->
                        <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('students.correction') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            [x-cloak] {
                display: none !important;
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

            /* Spinner styles */
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
        </style>
    @endpush
</x-FrontLayout>
