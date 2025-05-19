<x-FrontLayout>
    <style>
        @media print {
            .table-responsive {
                overflow: visible !important;
            }

            .table {
                width: 90%;
                table-layout: fixed;
            }

            th, td {
                word-wrap: break-word;
                white-space: normal;
            }

            /* Prevent cutting off rows at the bottom of the page */
            tr {
                page-break-inside: avoid;
            }
        .table {
            margin-bottom: 200px; /* Ensure space before the footer */
        }

        .table tr {
            height: auto; /* Adjust row height for printing */
        }
        }
    </style>

@php
function getSectionLetter($sectionId) {
    $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G','H'];
    return $letters[$sectionId - 1] ?? '';
}
@endphp

<div class="card text-left">
  <div class="card-body bg-info">
    <h4 class="card-title text-white fs-4 ">STUDENT'S ATTENDANCE REPORT  &nbsp;  &nbsp;
    <span class="badge bg-warning p-2">

    SCHOOL: {{$school}}
    </span>
    &nbsp;  &nbsp;
    <span class="badge bg-warning p-2">

        @if ($class<0)
                        @if ($class == -1)
                        CLASS: UKG-KG2-PP1
                        @endif
                        @if ($class == -2)
                            CLASS: LKG-KG1-PP2
                        @endif
                        @if ($class == -3)
                            CLASS:Nursery-KG-PP3
                        @endif
                @else
                            CLASS: {{$class}}

                @endif

        &nbsp;
        SECTION: {{getSectionLetter($section_id)}}
        </span>

        <span class="float-end">
            <button class="btn btn-primary" onclick="window.print()">PRINT</button>
            &nbsp;
            <a name="" id="" class="btn btn-primary" href="{{route('students.attendance')}}" role="button">BACK</a>
        </span>



    </h4>


  </div>
</div>

@if($results->isEmpty())
    <p>No attendance records found.</p>
@else
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr class="bg-info text-white text-uppercase">
                    <th>STUDENT PEN</th>

                    @foreach($results->first() as $date => $status)
                        @if($date !== 'student_pen')
                            <th>
                                @php
                                try {
                                    $formattedDate = (new DateTime($date))->format('d-m-Y');
                                } catch (Exception $e) {
                                    $formattedDate = $date; // fallback to original string if not a valid date
                                }
                            @endphp

                            {{ $formattedDate }}
                            </th>
                        @endif
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($results as $student)
                    <tr>
                        <td>{{ $student['student_pen'] }}</td>
                        @foreach($student as $date => $status)
                            @if($date !== 'student_pen')
                                <td>{{ $status }}</td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endif

<div class="mb-6" style="height: 100px;">
    &nbsp;
</div>

</x-FrontLayout>



