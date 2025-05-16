<x-FrontLayout>

    <div class="container-fluid mt-3">
        <div class="profile-foreground position-relative mx-n4 mt-n4">
            <div class="profile-wid-bg">
                {{-- <img src="assets/images/profile-bg.jpg" alt="" class="profile-wid-img" /> --}}
            </div>
        </div>
        <div class="pt-4 mb-4 mb-lg-3 pb-lg-4 profile-wrapper">
            @if (session('message'))
                <div class="alert bg-success text-white">
                    {{ session('message') }}
                </div>
            @endif
            <div class="row g-4">
                {{-- <div class="col-auto">
                    <div class="avatar-lg">
                        <img src="assets/images/users/avatar-1.jpg" alt="user-img" class="img-thumbnail rounded-circle" />
                    </div>
                </div> --}}
                <!--end col-->
                <div class="col">
                    <div class="p-2">
                        <h1 class="text-white mb-1">{{$user->tch_name}}</h1>
                        <p class="text-white text-opacity-75">{{$user->mobile_no}}</p>
                        <div class="hstack text-white-50 gap-1">
                            {{-- <div class="me-2"><i class="ri-map-pin-user-line me-1 text-white text-opacity-75 fs-16 align-middle"></i>California, United States</div> --}}
                            <div>
                                <i class="ri-building-line me-1 text-white text-opacity-75 fs-16 align-middle"></i> Date of Joining Service: {{$user->doj_service}}
                            </div>
                        </div>
                    </div>
                </div>
                <!--end col-->

                <!--end col-->

            </div>
            <!--end row-->
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div>
                    <div class="d-flex profile-wrapper">
                        <div class="flex-shrink-0">
                        <!-- Button trigger modal -->
                        <button
                            type="button"
                            class="btn btn-success btn-lg"
                            data-bs-toggle="modal"
                            data-bs-target="#modalId"
                        >
                        <i class="ri-edit-box-line align-bottom "></i> Edit Profile
                        </button>

                        </div>
                    </div>
                    <!-- Tab panes -->
                    <div class="tab-content pt-4 text-muted">
                        <div class="tab-pane active" id="overview-tab" role="tabpanel">

                            <!--end row-->
                        </div>
                    </div>
                    <!--end tab-content-->
                </div>
            </div>
            <!--end col-->
        </div>

        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-5 text-center bg-info p-4 text-white">UDISE CODE: {{$user->udise_sch_code}}</h5>

                                {{-- <div class="progress animated-progress custom-progress progress-label">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 30%" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100">
                                        <div class="label">30%</div>
                                    </div>
                                </div> --}}
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Info</h5>
                                <div class="table-responsive">
                                    <table class="table table-borderless mb-0">
                                        <tbody>
                                            <tr>
                                                <th class="ps-0" scope="row">Full Name :</th>
                                                <td class="text-muted">{{$user->tch_name}}</td>
                                            </tr>
                                            <tr>
                                                <th class="ps-0" scope="row">Mobile :</th>
                                                <td class="text-muted">{{$user->mobile_no}}</td>
                                            </tr>
                                            <tr>
                                                <th class="ps-0" scope="row">E-mail :</th>
                                                <td class="text-muted">{{$user->ema_il}}</td>
                                            </tr>
                                            <tr>
                                                <th class="ps-0" scope="row">DOB :</th>
                                                <td class="text-muted">{{$user->dob}}
                                                </td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div><!-- end card body -->
                        </div>


                            <!--end card-body-->
                        </div>
                        <!--end card-->
                    </div>
                    <!--end col-->

                    <!--end col-->
                </div>
            </div>
        </div>
    </div>



<!-- Modal -->
<div
    class="modal fade"
    id="modalId"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modalTitleId"
    aria-hidden="true"
>
    <div
        class="modal-dialog modal-lg "
        role="document"
    >
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">
                    UPDATE USER DATA
                </h5>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
            </div>
            <div class="modal-body">
                <form action="{{route('updateprofile')}}" method="post">
                    @csrf
                            <div>
                                <label for="basiInput" class="form-label">Name</label>
                                <input type="text" class="form-control" name="name" value="{{$user->tch_name}}">
                            </div>
                            <div>
                                <label for="basiInput" class="form-label mt-3">Phone Number</label>
                                <input type="text" class="form-control" name="mobile_no" value="{{$user->mobile_no}}">
                            </div>
                            <div>
                                <label for="basiInput" class="form-label mt-3">Email</label>
                                <input type="text" class="form-control" name="email" value="{{$user->ema_il}}">
                            </div>
                            <div>
                                <label for="basiInput" class="form-label mt-3">Date of Birth</label>
                                <input type="date" class="form-control" name="dob" value="{{$user->dob}}">
                            </div>

                            <button type="submit" class="btn btn-primary mt-5">Submit</button>
                </form>
                        </div>


            </div>
        </div>
    </div>



</div>


{{-- <div class="container mt-5">
    <button id="alertButton" class="btn btn-primary">Show Alert</button>
</div> --}}
        <!--end row-->

</x-FrontLayout>



