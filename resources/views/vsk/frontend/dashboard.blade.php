<x-FrontLayout>

@section('content')

@if (session('message'))
            <div class="alert alert-info">
                {{session('message')}}
            </div>
@endif

    {{-- {{$schooldata}} --}}
    {{-- <div class="row" >

        <div class="col-lg-6">

            <div class=" card text-left p-3 border shadow-lg">
                <h4 class="card-title bg-info text-white p-3 text-uppercase">Take Attendance &nbsp; <span class="badge bg-warning text-dark p-2"> Select Class</span></h4>

              <div class="card-body">
                <p class="card-text">
                    @foreach ($std_profile as $item)
                        <a name="" id="" class="p-2 m-2 btn btn-primary" href="{{route('stdclass',['id'=>$item->class_id, 'udise'=>$item->udise_cd])}}" role="button">CLASS {{$item->class_id}}</a>
                    @endforeach
                </p>
              </div>
            </div>

        </div>

        <div class="col-lg-6">

                <div class=" card text-left p-3 border shadow-lg">
                    <h4 class="card-title bg-info text-white p-3 text-uppercase">Generate Report &nbsp; <span class="badge bg-warning text-dark p-2"> Select Class</span></h4>
                  <div class="card-body">
                    <p class="card-text">
                        @foreach ($std_profile as $item)
                            <a name="" id="" class="p-2 m-2 btn btn-success" href="{{route('generatereport',['id'=>$item->class_id, 'udise'=>$item->udise_cd])}}" role="button">CLASS {{$item->class_id}}</a>
                        @endforeach
                    </p>
                  </div>
                </div>
        </div>




</div> --}}

    <div class="container py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-lg">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="mb-1 fw-bold">Teacher Dashboard</h4>
                                <p class="text-muted mb-0">Welcome back, {{ $tch_data->tch_profile->first()->tch_name ?? $user->name }}</p>
                            </div>
{{--                            <div class="d-flex align-items-center">--}}
{{--                                <div class="dropdown">--}}
{{--                                    <button class="btn btn-light dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">--}}
{{--                                        <i class="ri-user-line me-1"></i> My Account--}}
{{--                                    </button>--}}
{{--                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">--}}
{{--                                        <li><a class="dropdown-item" href="#"><i class="ri-user-settings-line me-2"></i>Profile</a></li>--}}
{{--                                        <li><a class="dropdown-item" href="#"><i class="ri-settings-3-line me-2"></i>Settings</a></li>--}}
{{--                                        <li><hr class="dropdown-divider"></li>--}}
{{--                                        <li><a class="dropdown-item text-danger" href="#"><i class="ri-logout-box-line me-2"></i>Logout</a></li>--}}
{{--                                    </ul>--}}
{{--                                </div>--}}
{{--                            </div>--}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Teacher Information -->
        <div class="row mb-4">
            <div class="col-lg-8 mb-4 mb-lg-0">
                <div class="card shadow-sm border-0 h-100 rounded-lg">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <h5 class="mb-0 fw-bold">Teacher Information</h5>
                    </div>
                    <div class="card-body">
                        @if($tch_data->tch_profile->first())
                            <div class="row">
                                <div class="col-md-3 mb-3 mb-md-0">
                                    <div class="text-center">
                                        <div class="avatar-lg mx-auto mb-3">
                                            <div class="avatar-title bg-primary-subtle text-primary rounded-circle fs-1">
                                                {{ substr($tch_data->tch_profile->first()->tch_name ?? 'T', 0, 1) }}
                                            </div>
                                        </div>
                                        <h6 class="fw-semibold">{{ $tch_data->tch_profile->first()->tch_name ?? 'N/A' }}</h6>
                                        <p class="text-muted small mb-0">Teacher ID: {{ $tch_data->tch_profile->first()->nat_tch_cd }}</p>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="table-responsive">
                                        <table class="table table-borderless">
                                            <tbody>
                                            <tr>
                                                <td class="fw-medium" width="35%">Email</td>
                                                <td>{{ $tch_data->tch_profile->first()->ema_il ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium">Phone</td>
                                                <td>{{ $tch_data->tch_profile->first()->mobile_no ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium">Designation</td>
                                                <td>{{ $tch_data->tch_profile->first()->designation ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium">Joining Date</td>
                                                <td>{{ $tch_data->tch_profile->first()->doj_service ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium">School Name</td>
                                                <td>{{ $schooldata->first()->school_name ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium">School UDISE</td>
                                                <td><span class="badge bg-info">{{ $tch_data->tch_profile->first()->udise_sch_code ?? 'N/A' }}</span></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info mb-0">
                                <i class="ri-information-line me-2"></i> Teacher profile information not found.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="col-lg-4">
                <div class="row h-100">
                    <div class="col-sm-6 col-lg-12 mb-4">
                        <div class="card shadow-sm border-0 rounded-lg bg-primary text-white h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-md bg-white bg-opacity-25 rounded-circle me-3">
                                        <i class="ri-user-follow-line fs-3 text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-white mb-1">Total Students</h6>
                                        <h3 class="text-white mb-0">{{ $total_students }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-12 mb-0">
                        <div class="card shadow-sm border-0 rounded-lg bg-success text-white h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-md bg-white bg-opacity-25 rounded-circle me-3">
                                        <i class="ri-school-line fs-3 text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-white mb-1">School District</h6>
                                        <h3 class="text-white mb-0">{{ $schooldata->first()->lgd_district_name ?? 'N/A' }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- School & Classes -->
        <div class="row">
            <!-- School Info -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm border-0 rounded-lg">
                    <div class="card-header bg-white border-bottom-0 d-flex justify-content-between align-items-center pt-4 pb-0">
                        <h5 class="mb-0 fw-bold">School Information</h5>
{{--                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editSchoolModal">--}}
{{--                            <i class="ri-edit-line me-1"></i> Edit--}}
{{--                        </button>--}}
                    </div>
                    <div class="card-body">
                        @if($schooldata->first())
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-md me-3">
                                    <div class="avatar-title bg-light rounded-circle text-primary">
                                        <i class="ri-building-line fs-3"></i>
                                    </div>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-1">{{ $schooldata->first()->school_name }}</h5>
                                    <span class="badge bg-info mb-0">UDISE: {{ $schooldata->first()->udise_sch_code }}</span>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tbody>
                                    <tr>
                                        <td class="fw-medium" width="40%">Address</td>
                                        <td>{{ $schooldata->first()->address ?? 'N/A' }}, {{ $schooldata->first()->pincode ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-medium">District</td>
                                        <td>{{ $schooldata->first()->lgd_district_name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-medium">Email</td>
                                        <td>{{ $schooldata->first()->email ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-medium">Established Year</td>
                                        <td>{{ $schooldata->first()->estd_year ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-medium">Head Master</td>
                                        <td>{{ $schooldata->first()->head_master_name ?? 'N/A' }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info mb-0">
                                <i class="ri-information-line me-2"></i> School information not found.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Classes List -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm border-0 rounded-lg h-100">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <h5 class="mb-0 fw-bold">Classes</h5>
                    </div>
                    <div class="card-body">
                        @if($std_by_class->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Class</th>
                                        <th scope="col">Count</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($std_by_class as $index => $class)
                                        <tr>
                                            <th scope="row">{{ $index + 1 }}</th>
                                            <td>
                                                @if($class->class_id == -3)
                                                    Nursery/KG/PP3
                                                @elseif($class->class_id == -2)
                                                    LKG/KG1/PP2
                                                @elseif($class->class_id == -1)
                                                    UKG/KG2/PP1
                                                @else
                                                    {{ $class->class_id }}
                                                @endif
                                                - Section
                                                @if($class->section_id == 1)
                                                    A
                                                @elseif($class->section_id == 2)
                                                    B
                                                @elseif($class->section_id == 3)
                                                    C
                                                @elseif($class->section_id == 4)
                                                    D
                                                @else
                                                    {{ $class->section_id }}
                                                @endif
                                            </td>
                                            <td>{{ $class->student_count }}</td>
                                            <td>
                                                <a href="{{ route('students.by-class-section', ['udise_cd' => $class->udise_cd, 'class_id' => $class->class_id, 'section_id' => $class->section_id]) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="ri-eye-line"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info mb-0">
                                <i class="ri-information-line me-2"></i> No classes found for this teacher.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>


    <!-- Edit School Modal -->
{{--    <div class="modal fade" id="editSchoolModal" tabindex="-1" aria-labelledby="editSchoolModalLabel" aria-hidden="true">--}}
{{--        <div class="modal-dialog modal-dialog-centered">--}}
{{--            <div class="modal-content">--}}
{{--                <div class="modal-header">--}}
{{--                    <h5 class="modal-title fw-bold" id="editSchoolModalLabel">Edit School Details</h5>--}}
{{--                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>--}}
{{--                </div>--}}
{{--                <div class="modal-body">--}}
{{--                    <form action="{{ route('updateschool') }}" method="post">--}}
{{--                        @csrf--}}
{{--                        <div class="mb-3">--}}
{{--                            <label for="headname" class="form-label">Head Master Name</label>--}}
{{--                            <input type="text" class="form-control" id="headname" name="headname" value="{{ $schooldata->first()->head_master_name ?? '' }}">--}}
{{--                            <input type="hidden" value="{{ $schooldata->first()->udise_sch_code ?? '' }}" name="udise">--}}
{{--                        </div>--}}
{{--                        <div class="mb-3">--}}
{{--                            <label for="email" class="form-label">School Email</label>--}}
{{--                            <input type="email" class="form-control" id="email" name="email" value="{{ $schooldata->first()->email ?? '' }}">--}}
{{--                        </div>--}}
{{--                        <div class="d-grid">--}}
{{--                            <button type="submit" class="btn btn-primary">Update School Details</button>--}}
{{--                        </div>--}}
{{--                    </form>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

    <style>
        /* Custom Styles */
        .avatar-md {
            height: 4rem;
            width: 4rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .avatar-lg {
            height: 5rem;
            width: 5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bg-primary-subtle {
            background-color: rgba(13, 110, 253, 0.15);
        }

        .timeline {
            position: relative;
            padding-left: 2rem;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 1.5rem;
        }

        .timeline-item:last-child {
            padding-bottom: 0;
        }

        .timeline-badge {
            position: absolute;
            left: -2rem;
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            color: #fff;
            text-align: center;
            line-height: 2rem;
        }

        .timeline:before {
            content: '';
            position: absolute;
            left: -0.25rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background-color: #e9ecef;
        }
    </style>

    <!-- Required Bootstrap JS (adjust path as needed) -->
{{--    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>--}}

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"  />

<script>
    $(document).ready(function()  
 {
        $('.searchable-select').select2();
    });
</script>
</x-FrontLayout>
