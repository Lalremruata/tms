<x-FrontLayout>
    @if (session('message'))
    <div class="alert bg-warning text-white fs-4">
        {{session('message')}}
    </div>
@endif


@php
    function getSectionLetter($sectionId) {
        $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G','H'];
        return $letters[$sectionId - 1] ?? '';
    }
@endphp

 <button id="updateButton" class="d-none">Update</button>

<div class="card text-left">
  <div class="card-body">
    <p class="card-text ">
        <span class=" badge bg-info p-3 fs-6">
            SCHOOL: {{$schoolname}} &nbsp; &nbsp;  <span class="badge bg-warning fs-6">

                @if ($classname<0)
                        @if ($classname == -1)
                        CLASS:  UKG-KG2-PP1
                        @endif
                        @if ($classname == -2)
                            CLASS: LKG-KG1-PP2
                        @endif
                        @if ($classname == -3)
                            CLASS: Nursery-KG-PP3
                        @endif
                @else
                            CLASS: {{$classname}}

                @endif

                &nbsp; SECTION:{{getSectionLetter($section_id)}}</span>
        </span>


        <span class="float-end">
        <a name="" id="" class="btn btn-primary" href="{{route('openstudentattendance')}}" role="button">BACK</a>

        </span>

    </p>
  </div>
</div>
 <div class="card border-primary">
   <div class="card-body">
     <h4 class="card-title bg-info p-3 fs-4 text-white">STUDENT'S  ATTENDANCE FOR :
{{ date('j F, Y') }} 
    
</h4>
     <p class="card-text">


        <form action="{{ route('submitatt') }}" method="POST">
            @csrf
            <div class="table-responsive">
            <table class="table  table-striped">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Student Name</th>
                        <th scope="col">Father Name</th>  
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($std_class_data as $item)
                    <tr>
                        <td scope="row">{{ $loop->iteration }}</td>
                        <td>{{ trim($item->student_name) }}</td>
                        <td>{{ $item->father_name }}</td>  
                        <td>
                            <div class="form-check form-switch form-switch-custom form-switch-danger mb-3">
                                <input class="form-check-input" name="attendance[{{ $loop->index }}][checked]" type="checkbox" role="switch" id="SwitchCheck{{ $loop->iteration }}" checked onchange="updateHiddenInputs(this, '{{ $item->student_pen }}', '{{ $item->udise_cd }}', '{{ $item->pres_class }}', '{{ $loop->index }}')">
                                <span class="badge rounded-pill bg-primary" id="StatusBadge{{ $loop->index }}">Present</span>
                                <input type="hidden" name="attendance[{{ $loop->index }}][status]" value="present" id="StatusInput{{ $loop->index }}">
                                <input type="hidden" name="attendance[{{ $loop->index }}][student_pen]" value="{{ $item->student_pen }}">
                                <input type="hidden" name="attendance[{{ $loop->index }}][udise_cd]" value="{{ $item->udise_cd }}">
                                <input type="hidden" name="attendance[{{ $loop->index }}][class_id]" value="{{ $item->pres_class }}">
                                <input type="hidden" name="attendance[{{ $loop->index }}][section_id]" value="{{ $item->section_id }}">
                            </div>
                        </td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
            <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>

        <div class="container mt-5">
            <button id="alertButton" class="btn btn-primary d-none" >Show Alert</button>
         </div>
        </p>
    </div>
  </div>
        <script>
            function updateHiddenInputs(checkbox, studentPen, udiseNo, classId, index) {
                let statusInput = document.getElementById('StatusInput' + index);
                let statusBadge = document.getElementById('StatusBadge' + index);

                if (checkbox.checked) {
                    statusInput.value = 'present';
                    statusBadge.textContent = 'Present';
                    statusBadge.className = 'badge rounded-pill bg-primary';
                } else {
                    statusInput.value = 'absent';
                    statusBadge.textContent = 'Absent';
                    statusBadge.className = 'badge rounded-pill bg-danger';
                }
            }

            // Ensure that the initial status is set correctly for all checkboxes
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.form-check-input').forEach((checkbox, index) => {
                    updateHiddenInputs(checkbox, checkbox.dataset.studentPen, checkbox.dataset.udiseCd, checkbox.dataset.classId, index);
                });
            });

                    // sweetlert
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('updateSuccess'))
                document.getElementById('updateButton').click();
            @endif
        });
        document.getElementById('updateButton').addEventListener('click', function () {
            updateSuccess();
        });
        document.getElementById('alertButton').addEventListener('click', function () {
            updateSuccess();
        });
        </script>




</x-FrontLayout>



