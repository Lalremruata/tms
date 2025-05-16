<?php

namespace App\Http\Controllers;

use App\Models\student_profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Std_profile;
use App\Models\Schoolmaster;
use App\Models\tch_profile;
use App\Models\Districtname;

class StudentDropboxController extends Controller
{
    private function getTeacherId()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return $tid = session('username');
    }

    public function index()
    {
        // Get teacher ID from session
        $tid = session('username');
        // Fetch districts for dropdown
        $distoption = Districtname::select('udise_district_code as dstcd', 'district_name as dname')
            ->join('sms.tch_profile as a', 'udise_district_code', '=', 'a.dstcd')
            ->where('a.tch_status', 'W')
            ->orderBy('district_name')
            ->distinct()
            ->get();

        return view('students/student-dropbox', compact('distoption'));
    }

    public function filter(Request $request)
    {
        // Get teacher ID from session
        $tid = session('username');

        // Fetch districts for dropdown (same as in index method)
        $distoption = Districtname::select('udise_district_code as dstcd', 'district_name as dname')
            ->join('sms.tch_profile as a', 'udise_district_code', '=', 'a.dstcd')
            ->where('a.tch_status', 'W')
            ->orderBy('district_name')
            ->distinct()
            ->get();

        // Validate request
        $request->validate([
            'parameter1' => 'nullable|string',  // district
//            'parameter2' => 'nullable|string',  // status
            'parameter3' => 'nullable|string',  // PEN
            'parameter4' => 'nullable|string',  // UDISE
        ]);

        $dst = $request->parameter1;
//        $stc = $request->parameter2;
        $pe = $request->parameter3;
        $udise = $request->parameter4;

        // Get teacher's school information
        $teacherSchool = DB::table('mizoram115.school_master as a')
            ->join('sms.tch_profile as b', 'a.udise_sch_code', '=', 'b.udise_sch_code')
            ->select('a.udise_sch_code as chk', 'a.school_name', 'a.district_cd',
                DB::raw("(SELECT district_name FROM mizoram115.mst_district WHERE udise_district_code = a.district_cd) as dstname"),
                'a.block_cd')
            ->where('b.slno', $tid)
            ->where('b.tch_status', 'W')
            ->first();

        $rows = null;
        $rw = [];

        if ($teacherSchool) {
            $rw[] = (array)$teacherSchool;
            $ucd = $teacherSchool->chk;

            // Base query with common select fields
            $query = student_profile::select(
                'udise_cd', 'student_pen', 'student_name', 'gender',
                DB::raw("(SELECT c.school_name FROM mizoram115.school_master c WHERE c.udise_sch_code = udise_cd) AS schname"),
                DB::raw("(SELECT COUNT(*) FROM sms.student_profile WHERE udise_cd = sms.student_profile.udise_cd AND pres_class = sms.student_profile.pres_class AND section_id = sms.student_profile.section_id) AS nos"),
                'student_dob', 'father_name', 'mother_name', 'mobile_no_1', 'section_id',
                'class_id',
                DB::raw("(CASE
                    WHEN class_id = -1 THEN 'UKG/KG2/PP1'
                    WHEN class_id = -2 THEN 'LKG/KG1/PP2'
                    WHEN class_id = -3 THEN 'Nursery/KG/PP3'
                    WHEN class_id = 1 THEN '1'
                    WHEN class_id = 2 THEN '2'
                    WHEN class_id = 3 THEN '3'
                    WHEN class_id = 4 THEN '4'
                    WHEN class_id = 5 THEN '5'
                    WHEN class_id = 6 THEN '6'
                    WHEN class_id = 7 THEN '7'
                    WHEN class_id = 8 THEN '8'
                    WHEN class_id = 9 THEN '9'
                    WHEN class_id = 10 THEN '10'
                    WHEN class_id = 11 THEN '11'
                    WHEN class_id = 12 THEN '12'
                END) AS clsid"),
                'pres_class', 'stud_status'
            );

            // Apply filters based on inputs
            if (!empty($udise)) {
                // Filter by UDISE code
                $query->where('udise_cd', $udise)
                    ->whereNotIn('stud_status', ['E', 'P']);
//                $query->where('udise_cd', $udise);
            } elseif (!empty($pe)) {
                // Filter by student PEN
                $query->where('student_pen', $pe)
                    ->whereNotIn('stud_status', ['E', 'P']);
//                $query->where('student_pen', $pe);
            } elseif (!empty($dst)) {
                // Filter by district
                $query->join('mizoram115.school_master as b', 'udise_cd', '=', 'b.udise_sch_code')
                    ->where('b.district_cd', $dst)
                    ->whereNotIn('stud_status', ['E', 'P']);
//                $query->join('mizoram115.school_master as b', 'udise_cd', '=', 'b.udise_sch_code')
//                    ->where('b.district_cd', $dst);
                // Apply status filter if provided
//                if (!empty($stc)) {
//                    $query->where('stud_status', $stc);
//                }
            }else{
                $query->where('udise_cd', $teacherSchool->chk)
                    ->whereNotIn('stud_status', ['E', 'P'])
                    ->limit(50);
            }

            // Common conditions and pagination
            $rows = $query->orderBy('student_name')
                ->paginate(50);
        } else {
            return back()->with('error', 'Only Teacher from This School can use the Dropbox Facility, Please Select another Teacher ...!!!');
        }

        return view('students/student-dropbox', compact('distoption', 'rows', 'rw'));
    }

    public function update(Request $request)
    {
        // Get teacher ID from session
        $tid = session('username');

        // Validate request
        $request->validate([
            'save' => 'required|string',
            'status' => 'required|in:E,P',
        ]);

        $slno = $request->save;  // This is the student PEN

        // Check if student is already Enrolled or Pending
        $student = Std_profile::where('student_pen', $slno)->first();
        if ($student && in_array($student->stud_status, ['E', 'P'])) {
            return back()->with('error', 'Cannot update students who are already Enrolled or Pending');
        }
        // Directly access the status value
//        if (!isset($request->status[$slno])) {
//            return back()->with('error', 'Status information missing for student');
//        }
//
        $status = $request->status;

        // Get teacher's school information
        $teacherSchool = DB::table('mizoram115.school_master as a')
            ->join('sms.tch_profile as b', 'a.udise_sch_code', '=', 'b.udise_sch_code')
            ->select('a.udise_sch_code as chk', 'a.school_name', 'a.district_cd', 'a.block_cd')
            ->where('b.slno', $tid)
            ->first();

        if ($teacherSchool && !empty($teacherSchool->chk)) {
            $ucd = $teacherSchool->chk;
            $dstcd = $teacherSchool->district_cd;
            $blkcd = $teacherSchool->block_cd;
            $sname = $teacherSchool->school_name;

            // Update student record
            try {
                student_profile::where('student_pen', $slno)
                    ->update([
                        'stud_status' => trim($status),
                        'udise_cd' => trim($ucd),
                        'school_name' => trim($sname),
                        'district_cd' => trim($dstcd),
                        'block_cd' => trim($blkcd)
                    ]);

                return back()->with('success', 'Student Transferred Successfully');
            }
            catch (\Exception $e) {
                return back()->with('error', 'Error updating student: ' . $e->getMessage());
            }

        } else {
            return back()->with('error', 'Error in Transfer, Please login again... Session Out....');
        }
    }
}
