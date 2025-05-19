<x-FrontLayout>

    @section('content')

        @if (session('message'))
            <div class="alert alert-info fs-5 text-primary">
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
        {{-- {{$schooldata}} --}}
        <div class="row" >

            {{-- @foreach ($std_profile as $item)
            {{ $item['pres_class'] }}
            @endforeach --}}

            <div class="col-lg-6">

                <div class=" card text-left p-3 border shadow-lg">
                    <h4 class="card-title bg-info text-white p-3 text-uppercase">Take Attendance &nbsp; <span class="badge bg-warning text-dark p-2"> Select Class</span></h4>

                    <div class="card-body">
                        <p class="card-text ">
                            @foreach ($std_profile1 as $item)
                                @if ($item->status =='1')
                                    <a class="p-2 m-2 btn btn-primary disabled"
                                       href="{{ route('stdclass', ['id' => $item->pres_class, 'udise' => $item->udise_cd, 'section_id' => $item->section_id]) }}"
                                       role="button"
                                       style="width: 100px;"> <!-- Set your desired width here -->
                                        {{-- CLASS {{$item->pres_class}} --}}

                                        @if ($item->pres_class<0)
                                            @if ($item->pres_class == -1)
                                                CLASS: UKG/KG2/PP1
                                            @endif
                                            @if ($item->pres_class == -2)
                                                CLASS: LKG/KG1/PP2
                                            @endif
                                            @if ($item->pres_class == -3)
                                                CLASS: Nursery/KG/PP3
                                            @endif
                                        @else
                                            CLASS: {{$item->pres_class}}

                                        @endif



                                        {{-- <span class="badge bg-info">SECTION: {{$item->section_id}}</span> --}}
                                        <span class="badge bg-info">
                            Section: {{ getSectionLetter($item->section_id) }}
                       </span>
                                    </a>

                                @else

                                    <a class="p-2 m-2 btn btn-primary"
                                       href="{{ route('stdclass', ['id' => $item->pres_class, 'udise' => $item->udise_cd, 'section_id' => $item->section_id]) }}"
                                       role="button"
                                       style="width: 100px;"> <!-- Set your desired width here -->
                                        {{-- CLASS {{$item->pres_class}} --}}

                                        @if ($item->pres_class<0)
                                            @if ($item->pres_class == -1)
                                                CLASS: UKG/KG2/PP1
                                            @endif
                                            @if ($item->pres_class == -2)
                                                CLASS: LKG/KG1/PP2
                                            @endif
                                            @if ($item->pres_class == -3)
                                                CLASS: Nursery/KG/PP3
                                            @endif
                                        @else
                                            CLASS: {{$item->pres_class}}

                                        @endif



                                        {{-- <span class="badge bg-info">SECTION: {{$item->section_id}}</span> --}}
                                        <span class="badge bg-info">
                            Section: {{ getSectionLetter($item->section_id) }}
                       </span>
                                    </a>

                                @endif
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
                                <a class="p-2 m-2 btn btn-primary"
                                   href="{{ route('generatereport', ['id' => $item->pres_class, 'udise' => $item->udise_cd, 'section_id' => $item->section_id]) }}"
                                   role="button"
                                   style="width: 100px;"> <!-- Set your desired width here -->
                                    @if ($item->pres_class<0)
                                        @if ($item->pres_class == -1)
                                            CLASS: UKG/KG2/PP1
                                        @endif
                                        @if ($item->pres_class == -2)
                                            CLASS: LKG/KG1/PP2
                                        @endif
                                        @if ($item->pres_class == -3)
                                            CLASS: Nursery/KG/PP3
                                        @endif
                                    @else
                                        CLASS: {{$item->pres_class}}

                                    @endif
                                    <span class="badge bg-info">
                            Section: {{ getSectionLetter($item->section_id) }}
                       </span>
                                </a>
                                {{-- <a name="" id="" class="p-2 m-2 btn btn-primary" href="{{route('generatereport',['id'=>$item->pres_class, 'udise'=>$item->udise_cd, 'section_id'=>$item->section_id])}}" role="button">CLASS {{$item->pres_class}} <span class="badge bg-info">SECTION: {{$item->section_id}}</span></a> --}}
                            @endforeach
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card text-left p-3 border shadow-lg">
                    <h4 class="card-title bg-info text-white p-3 text-uppercase">Summary of Today Attendance</h4>

                    @foreach ($std_profile1 as $item)
                        @if ($item->status == '1')
                            <div class="card mb-3 {{ $loop->odd ? 'bg-light' : 'bg-light' }} text-dark p-3">
                                <h5 class="card-subtitle mb-2 text-primary">
                                    @if ($item->pres_class < 0)
                                        @switch($item->pres_class)
                                            @case(-1)
                                                CLASS: UKG/KG2/PP1
                                                &nbsp;  <span class="bg-success text-white p-2 border rounded">Total : @php
                                                        $total= $item->present + $item->absent
                                                    @endphp
                                                    {{$total}}
                                </span> &nbsp; <span class="bg-info text-white p-2">Present : {{$item->present}}</span> &nbsp;  <span class="bg-info text-white p-2">Absent : {{$item->absent}}</span>
                                                @break

                                            @case(-2)
                                                CLASS: LKG/KG1/PP2
                                                &nbsp;  <span class="bg-success text-white p-2 border rounded">Total : @php
                                                        $total= $item->present + $item->absent
                                                    @endphp
                                                    {{$total}}
                                </span> &nbsp; <span class="bg-info text-white p-2">Present : {{$item->present}}</span> &nbsp;  <span class="bg-info text-white p-2">Absent : {{$item->absent}}</span>

                                                @break

                                            @case(-3)
                                                CLASS: Nursery/KG/PP3
                                                &nbsp;  <span class="bg-success text-white p-2 border rounded">Total : @php
                                                        $total= $item->present + $item->absent
                                                    @endphp
                                                    {{$total}}
                                </span> &nbsp; <span class="bg-info text-white p-2">Present : {{$item->present}}</span> &nbsp;  <span class="bg-info text-white p-2">Absent : {{$item->absent}}</span>
                                                @break

                                            @default
                                                CLASS: Unknown
                                        @endswitch
                                    @else
                                        CLASS: {{ $item->pres_class }} &nbsp;  <span class="bg-success text-white p-2 border rounded">Total : @php
                                                $total= $item->present + $item->absent
                                            @endphp
                                            {{$total}}
                                </span> &nbsp; <span class="bg-info text-white p-2">Present : {{$item->present}}</span> &nbsp;  <span class="bg-info text-white p-2">Absent : {{$item->absent}}</span>
                                    @endif
                                </h5>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>




        </div>

        <script>
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
