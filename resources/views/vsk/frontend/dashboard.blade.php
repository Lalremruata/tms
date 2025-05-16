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

<div class="row">
    <div class="col-md-12">


        @foreach ($schooldata as $item)
                <div class="card pricing-box shadow-lg rounded-lg">
                    <div class="card-body p-4 m-2">
                        <div class="float-end">
                            &nbsp;
                            {{-- <a name="" id="" class="btn btn-primary" href="#" role="button">EDIT</a> --}}
                           <!-- Modal trigger button -->
                           <button
                            type="button"
                            class="btn btn-primary btn-lg"
                            data-bs-toggle="modal"
                            data-bs-target="#modalId"
                           >
                            EDIT
                           </button>

                           <!-- Modal Body -->
                           <!-- if you want to close by clicking outside the modal, delete the last endpoint:data-bs-backdrop and data-bs-keyboard -->
                           <div
                            class="modal fade"
                            id="modalId"
                            tabindex="-1"
                            data-bs-backdrop="static"
                            data-bs-keyboard="false"

                            role="dialog"
                            aria-labelledby="modalTitleId"
                            aria-hidden="true"
                           >
                            <div
                                class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-md"
                                role="document"
                            >
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalTitleId">
                                           EDIT SHCOOL DETAILS
                                        </h5>
                                        <button
                                            type="button"
                                            class="btn-close"
                                            data-bs-dismiss="modal"
                                            aria-label="Close"
                                        ></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{route('updateschool')}}" method="post">
                                            @csrf

                                            <label for="schoolname" class="">Head Master Name</label>
                                            <input type="text" name="headname" id="" value="{{$item->head_master_name}}" class="form-control mt-2">
                                            <input type="hidden" value="{{$item->udise_sch_code}}" name="udise">

                                            <label for="email" class="mt-3">School Email</label>
                                            <input type="email" name="email" id="" value="{{$item->email}}" class="form-control">

                                            <button type="submit" class="btn btn-primary mt-2">Submit</button>
                                        </form>
                                    </div>

                                </div>
                            </div>
                           </div>

                           <!-- Optional: Place to the bottom of scripts -->
                           <script>
                            const myModal = new bootstrap.Modal(
                                document.getElementById("modalId"),
                                options,
                            );
                           </script>

                        </div>
                        <div>
                            <div class="d-flex align-item-center">
                                <div class="flex-grow-1">
                                    <h5 class="mb-1 fw-bold">School Name: {{$item->school_name}} &nbsp; &nbsp;</h5>
                                    <p class="d-flex center"> <span class="bg-info text-white p-1 ">UDISE CODE: {{$item->udise_sch_code}}</span></p>
                                    <p class="text-muted mb-0">{{$item->address}}</p>
                                    <p class="text-muted mb-0">{{$item->pincode}}</p>
                                </div>
                                <div class="avatar-sm">
                                    <div class="avatar-title bg-light rounded-circle text-primary">
                                        <i class="ri-stack-line fs-3"></i>
                                    </div>
                                </div>
                            </div>


                        </div>
                        <hr class="my-4 text-muted">
                        <div>
                            <ul class="list-unstyled vstack gap-3 text-muted">

                                <div class="container ">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="table-responsive table">
                                                <table class="table table-bordered table-striped-columns">
                                                    <thead>
                                                        <tr>
                                                            <th>District</th>
                                                            <th>Email</th>
                                                            <th>Est. Year</th>
                                                            <th>Head Name</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>{{$item->lgd_district_name}}</td>
                                                            <td>{{$item->email}}</td>
                                                            <td>{{$item->estd_year}}</td>
                                                            <td>{{$item->head_master_name}}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </ul>
                            {{-- <div class="mt-4">
                                <a href="javascript:void(0);" class="btn btn-soft-success w-100 waves-effect waves-light">Get started</a>
                            </div> --}}
                        </div>
                    </div>
                    {{-- <div class="card text-left">
                        <div class="card-body">
                          <p class="card-text">
                              <a name="" id="" class="btn btn-primary" href="#" role="button">EDIT</a>
                          </p>
                        </div>
                    </div> --}}
                </div>


                @endforeach

            </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"  />

<script>
    $(document).ready(function()  
 {
        $('.searchable-select').select2();
    });
</script>
</x-FrontLayout>
