<?php

namespace App\Http\Controllers\Vsk\frontend;
use App\Http\Controllers\Controller;
use App\Models\tch_data;
use Illuminate\Http\Request;
use App\Models\Session;
use App\Models\tch_profile;
use App\Models\Std_profile;
use App\Models\Schoolmaster;
use App\Models\student_attendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;


class AttendanceController extends Controller
{
    public function showDashboard()
    {
        return view('vsk.frontend.dashboard');
    }

    public function index()
    {

        $user=Auth::user();

        $tch_data=tch_data::where('id',$user->id)->with('tch_profile')->first();
        $std_profile= $tch_data->tch_profile->first();
        // return $std_profile->udise_sch_code;
        $std_profile=Std_profile::where('udise_cd',$std_profile->udise_sch_code)->select('pres_class','udise_cd','section_id')->where('stud_status','E')->distinct()->orderBy('pres_class')->get();
//  return $std_profile;
        $filteredData = [];
        $currentDate = date('Y-m-d');
        $filteredData = [];

        foreach ($std_profile as $profile) {
            $exists = student_attendance::where('date', $currentDate)
                ->where('udise_cd', $profile['udise_cd'])
                ->where('class_id', $profile['pres_class'])
                ->where('section_id', $profile['section_id'])
                ->exists();

            // Add 'status' key based on existence
            if ($exists) {
                $profile['status'] = 1;
                $present=student_attendance::where('date', $currentDate)
                    ->where('udise_cd', $profile['udise_cd'])
                    ->where('class_id', $profile['pres_class'])
                    ->where('section_id', $profile['section_id'])
                    ->where('status','true')
                    ->count();

                $absent=student_attendance::where('date', $currentDate)
                    ->where('udise_cd', $profile['udise_cd'])
                    ->where('class_id', $profile['pres_class'])
                    ->where('section_id', $profile['section_id'])
                    ->where('status','false')
                    ->count();

                $profile['present'] = $present;
                $profile['absent'] = $absent;

            } else {
                $profile['status'] = 0;
            }

            $filteredData[] = $profile;
        }

        $std_profile1 = $filteredData;
        // return $std_profile1;

        foreach($std_profile1 as $count)
        {
            if($count->status==1)
            {
                $presab= student_attendance::where('data', $currentDate)
                    ->where('udise_cd', $profile['udise_cd'])
                    ->where('class_id', $profile['pres_class'])
                    ->where('section_id', $profile['section_id']);
            }
        }


        $schooldata= $tch_data->tch_profile->first();
        $schooldata = Schoolmaster::where('udise_sch_code',$schooldata->udise_sch_code)->get();
        // return $schooldata;

        // $schoollist=Schoolmaster::select('udise_sch_code', 'school_name')->get();

        $tch_profile=tch_data::where('slno',$user->slno)->with('tch_profile')->first();

        return view('students.attendance-dashboard',compact('user','tch_data','tch_profile','schooldata','std_profile','std_profile1')); // Pass the user to the view
    }

    public function fetchAttendanceData(Request $request)
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        // Fetch attendance data between the given dates
        // Replace the following example with your actual data fetching logic
        $attendanceData = [
            'present' => 70,
            'absent' => 30,
        ];

        return response()->json($attendanceData);
    }

    public function submitattendance(Request $request)
    {
        // return $request;
        //   // Store data in the session
        //   $request->session()->put('user_id', '1000');
        //   return response()->json(['message' => 'Session data stored']);
        return Auth::user()->id;
        $existingSession = Session::where('user_id', $user_id)
                ->where('last_activity', '>=', Carbon::now()->subHours(24)->timestamp)
                ->first();

        return $existingSession;

    }

    public function stdclass($id,$udise,$section_id)
    {
        $std_class_data=Std_profile::where('udise_cd',$udise)->where('pres_class',$id)->where('section_id',$section_id)->where('stud_status','E')->orderBy('student_name', 'asc')->get();
        if ($std_class_data->isNotEmpty()) {
            $school =$std_class_data->first();
            $schoolname= $school->school_name;
             $classname= $school->class_id;
             $section_id=$section_id;
            return view('vsk.frontend.std_class',compact('std_class_data','schoolname','classname','section_id'));
        }
        else
        {
            return redirect()->back()->with([
                'message' => "NO STUDENT ENROLMENT FOUND IN THE SELECTED CLASS AND SECTION",
            ]);
        }

    }

    public function submitatt (Request $request)
    {

        $user = Auth::user();
        $temp= $request->attendance[0];
        // return $temp['student_pen'];

        $check = student_attendance::where('udise_cd', $temp['udise_cd'])
        ->where('class_id', $temp['class_id'])
        ->where('section_id', $temp['section_id'])
        ->latest()
        ->first();
        if($check)
        {
             // Get today's date
        $today = date('Y-m-d');
        // $today = '2024-08-09';


        // Check if the appointment date is the same as today's date
        if ($check->date==$today) {
            $name=$check->teacher_code;
            $name= tch_profile::where('slno',$name)->select('tch_name')->get();
            $name = $name[0]['tch_name'];

            return redirect()->route('students.attendance')->with([
                'message' => "Attendance Already Submitted for Today By: $name",
                'updateSuccess' => false
            ]);

        }

        }



        // $status=student_attendance::where('')

        foreach ($request->attendance as $data) {
            if (isset($data['checked']) && $data['checked'] == 'on') {
                $status='1';
            }
            else{
                $status='0';
            }

            student_attendance::create([
                'time' => date('H:i:s'),  // Store the time here
                'date' => date('Y-m-d'),  // Store the date here
                'teacher_code' => $user->slno,
                'status' => $status,
                'student_pen' => $data['student_pen'],
                'udise_cd' => $data['udise_cd'],
                'class_id' => $data['class_id'],
                'section_id' => $data['section_id'],
            ]);
        }
        // Redirect or return a response
        // return redirect()->back()->with(['message' => 'Attendance Submitted successfully.', 'updateSuccess' => true]);
        return redirect()->route('students.attendance')->with(['message' => 'Attendance Submitted successfully.', 'updateSuccess' => true]);

    }

    public function generatereport($id, $udise,$section_id){
        $classes=$id;
        $udise=$udise;
        $section_id=$section_id;
        return view('vsk.frontend.generatereport',compact('udise','classes','section_id'));
    }

    public function generatereportdate(Request $request)
    {
        $sdate = $request->sdate;
        $edate = $request->edate;
        $school = Schoolmaster::where('udise_sch_code', $request->udise)->first();


        $attendances = student_attendance::whereBetween('date', [$sdate, $edate])
            ->where('class_id', $request->class)
            ->where('udise_cd', $request->udise)
            ->orderBy('date') // Optional: Order by date if not already ordered
            ->with('getstudent')
            ->get();

        if ($attendances->isEmpty()) {
            return redirect()->back()->with(['message' => 'No Attendance Found for the selected date and class']);
        } else {
            $schoolandclass = $attendances->first();
            $school = $schoolandclass->getstudent->school_name;
            $class = $schoolandclass->getstudent->class_id;
            $section_id=$request->section_id;

            // Transform the data into a format suitable for displaying
            $results = $attendances->groupBy('student_pen')->map(function ($group) {
                $result = [
                    'student_pen' => $group->first()->getstudent->student_pen,
                    'STUDENT NAME' => $group->first()->getstudent->student_name,
                    'FATHER NAME' => $group->first()->getstudent->father_name,
                ];
                foreach ($group as $attendance) {
                    $result[$attendance->date] = $attendance->status ? 'Present' : 'Absent';
                }
                return $result;
            })->sortBy('STUDENT NAME');

            if ($request->has('export') && $request->export === 'excel') {
                return $this->exportToExcel($results, $school, $class);
            }

            return view('vsk.frontend.attendancereport', compact('results', 'school', 'class','section_id'));
        }
    }

    private function exportToExcel($results, $school, $class)
    {
        return Excel::download(new class($results) implements FromArray, WithHeadings {
            private $results;

            public function __construct($results)
            {
                $this->results = $results;
            }

            public function array(): array
            {
                $data = [];
                foreach ($this->results as $student) {
                    $row = [$student['student_pen'], $student['STUDENT NAME'], $student['FATHER NAME']];
                    foreach ($student as $date => $status) {
                        if ($date !== 'student_pen' && $date !== 'STUDENT NAME' && $date !== 'FATHER NAME') {
                            $row[] = $status;
                        }
                    }
                    $data[] = $row;
                }
                return $data;
            }

            public function headings(): array
            {
                $headings = ['STUDENT PEN', 'STUDENT NAME', 'FATHER NAME'];
                if (!empty($this->results)) {
                    foreach ($this->results->first() as $date => $status) {
                        if ($date !== 'student_pen' && $date !== 'STUDENT NAME' && $date !== 'FATHER NAME') {
                            try {
                                $formattedDate = (new \DateTime($date))->format('d-m-Y');
                            } catch (\Exception $e) {
                                $formattedDate = $date;
                            }
                            $headings[] = $formattedDate;
                        }
                    }
                }
                return $headings;
            }
        }, 'attendance_report.xlsx');
    }


}
