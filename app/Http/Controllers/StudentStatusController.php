<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\student_profile;
use App\Models\SchoolMaster;
use App\Models\tch_profile;
use App\Models\Districtname;
use Carbon\Carbon;

class StudentStatusController extends Controller
{
    /**
     * Get the username from session
     *
     * @return string|null
     */
    private function getTeacherId()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return session('username');
    }

    /**
     * Display the student status check form
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('students.student-status');
    }

    /**
     * Check the student's status based on PEN
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function checkStatus(Request $request)
    {
        // Get teacher ID from session
        $tid = $this->getTeacherId();

        // Validate request
        $request->validate([
            'parameter3' => 'required|string',  // Student PEN
        ]);

        $pe = $request->parameter3;

        // Get teacher's school information
        $teacherSchool = DB::table('mizoram115.school_master as a')
            ->join('sms.tch_profile as b', 'a.udise_sch_code', '=', 'b.udise_sch_code')
            ->select(
                'a.udise_sch_code as chk',
                'a.school_name',
                'a.district_cd',
                DB::raw("(SELECT district_name FROM mizoram115.mst_district WHERE udise_district_code = a.district_cd) as dstname"),
                'a.block_cd'
            )
            ->where('b.slno', $tid)
            ->where('b.tch_status', 'W')
            ->first();

        $rows = null;
        $rw = [];

        if ($teacherSchool) {
            $rw[] = (array)$teacherSchool;

            // Query student status and information
            $rows = DB::table('sms.student_profile as a')
                ->join('mizoram115.school_master as b', 'a.udise_cd', '=', 'b.udise_sch_code')
                ->select(
                    'a.udise_cd',
                    DB::raw("CASE
                        WHEN a.stud_status = 'E' THEN 'Enrolled'
                        WHEN a.stud_status = 'W' THEN 'Wrong Entry'
                        WHEN a.stud_status = 'T' THEN 'TC Taken'
                        WHEN a.stud_status = 'A' THEN 'Long Absentees'
                        WHEN a.stud_status = 'D' THEN 'Demised'
                        WHEN a.stud_status = 'P' THEN 'Pending'
                        WHEN a.stud_status = 'O' THEN 'Passed Out'
                        END AS ststatus"),
                    DB::raw("CASE
                        WHEN a.stud_status IN ('E') THEN 'Cannot Transfer to Your School, If You want to Enrol this Student then the Previous School must Mark this Student as TC Taken'
                        WHEN a.stud_status IN ('W','T','A','O') THEN 'Use DropBox Facility'
                        WHEN a.stud_status IN ('P') THEN 'Use Transfer from Another School Facility'
                        END AS trf"),
                    'a.student_pen',
                    'a.student_name',
                    'a.gender',
                    DB::raw("(SELECT c.school_name FROM mizoram115.school_master c WHERE c.udise_sch_code = a.udise_cd) AS schname"),
                    DB::raw("(SELECT c.district_name FROM mizoram115.mst_district c WHERE c.udise_district_code = b.district_cd) AS dstname"),
                    'a.student_dob',
                    'a.father_name',
                    'a.mother_name',
                    'a.mobile_no_1',
                    'a.section_id',
                    'a.class_id',
                    DB::raw("CASE
                        WHEN a.class_id = -1 THEN 'Class UKG/KG2/PP1'
                        WHEN a.class_id = -2 THEN 'Class LKG/KG1/PP2'
                        WHEN a.class_id = -3 THEN 'Class Nursery/KG/PP3'
                        WHEN a.class_id = 1 THEN 'Class 1'
                        WHEN a.class_id = 2 THEN 'Class 2'
                        WHEN a.class_id = 3 THEN 'Class 3'
                        WHEN a.class_id = 4 THEN 'Class 4'
                        WHEN a.class_id = 5 THEN 'Class 5'
                        WHEN a.class_id = 6 THEN 'Class 6'
                        WHEN a.class_id = 7 THEN 'Class 7'
                        WHEN a.class_id = 8 THEN 'Class 8'
                        WHEN a.class_id = 9 THEN 'Class 9'
                        WHEN a.class_id = 10 THEN 'Class 10'
                        WHEN a.class_id = 11 THEN 'Class 11'
                        WHEN a.class_id = 12 THEN 'Class 12'
                        END AS clsid"),
                    'a.pres_class',
                    'a.stud_status'
                )
                ->where('a.student_pen', $pe)
                ->get();

            // Format dates for display
            foreach ($rows as $row) {
                if (!empty($row->student_dob)) {
                    try {
                        $date = Carbon::parse($row->student_dob);
                        $row->formatted_dob = $date->format('Y-m-d');
                    } catch (\Exception $e) {
                        $row->formatted_dob = 'Date format error';
                    }
                }
            }
        } else {
            return back()->with('error', 'Only Teacher from This School can use the Transfer from Other School Facility, Please Select another Teacher ...!!!');
        }

        return view('students.student-status', compact('rows', 'rw', 'pe'));
    }
}
