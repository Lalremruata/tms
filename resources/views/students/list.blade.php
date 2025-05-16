<x-FrontLayout>
    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex align-items-center">
                    <h1 class="mb-0 text-center">Student List</h1>
                </div>
            </div>
        </div>

        @if($schoolInfo)
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <strong class="text-dark">District:</strong>
                            <span class="text-primary">{{ $schoolInfo->district_name }}</span>
                        </div>
                        <div class="col-md-3 mb-2">
                            <strong class="text-dark">Block:</strong>
                            <span class="text-primary">{{ $schoolInfo->block_name }}</span>
                        </div>
                        <div class="col-md-3 mb-2">
                            <strong class="text-dark">Udise Code:</strong>
                            <span class="text-primary">{{ $schoolInfo->udise_sch_code }}</span>
                        </div>
                        <div class="col-md-3 mb-2">
                            <strong class="text-dark">School Name:</strong>
                            <span class="text-primary">{{ $schoolInfo->school_name }}</span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-2">
                        <a href="{{ route('students.export') }}" class="btn btn-success me-2" title="Export to Excel">
                            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 48 48">
                                <path fill="#4CAF50" d="M41,10H25v28h16c0.553,0,1-0.447,1-1V11C42,10.447,41.553,10,41,10z"></path><path fill="#FFF" d="M32 15H39V18H32zM32 25H39V28H32zM32 30H39V33H32zM32 20H39V23H32zM25 15H30V18H25zM25 25H30V28H25zM25 30H30V33H25zM25 20H30V23H25z"></path><path fill="#2E7D32" d="M27 42L6 38 6 10 27 6z"></path><path fill="#FFF" d="M19.129,31l-2.411-4.561c-0.092-0.171-0.186-0.483-0.284-0.938h-0.037c-0.046,0.215-0.154,0.541-0.324,0.979L13.652,31H9.895l4.462-7.001L10.274,17h3.837l2.001,4.196c0.156,0.331,0.296,0.725,0.42,1.179h0.04c0.078-0.271,0.224-0.68,0.439-1.22L19.237,17h3.515l-4.199,6.939l4.316,7.059h-3.74V31z"></path>
                            </svg> Export Excel
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-warning">
                No school information found for your account. Please contact the administrator.
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-primary">
                        <tr>
                            <th class="text-center">Student Code</th>
                            <th class="text-center">Student PEN</th>
                            <th class="text-center">Student Name</th>
                            <th class="text-center">Gender</th>
                            <th class="text-center">Date of Birth</th>
                            <th class="text-center">Father Name</th>
                            <th class="text-center">Mother Name</th>
                            <th class="text-center">Mobile No.</th>
                            <th class="text-center">Class</th>
                            <th class="text-center">Section</th>
                            <th class="text-center">Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($students as $student)
                            <tr>
                                <td class="text-center">{{ $student->userid }}</td>
                                <td class="text-center">{{ $student->student_pen ?? '-' }}</td>
                                <td>{{ $student->student_name }}</td>
                                <td class="text-center">{{ $student->gender }}</td>
                                <td class="text-center">{{ $student->student_dob }}</td>
                                <td>{{ $student->father_name }}</td>
                                <td>{{ $student->mother_name }}</td>
                                <td class="text-center">{{ $student->mobile_no_1 }}</td>
                                <td class="text-center">{{ $student->clsid }}</td>
                                <td class="text-center">{{ $student->section_name ?? $student->section_id }}</td>
                                <td class="text-center">
                                    @if($student->stud_status == 'Enrolled' || $student->stud_Status == 'Enrolled')
                                        <span class="badge bg-success">Enrolled</span>
                                    @elseif($student->stud_status == 'Pending' || $student->stud_Status == 'Pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @else
                                        {{ $student->stud_status ?? $student->stud_Status }}
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center">No students found</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-FrontLayout>
