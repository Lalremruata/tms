<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Teacher;
use App\Models\School;
use App\Models\Student;
use App\Exports\StudentsExport;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    public function index()
    {
        // Get the username from session instead of Auth::id()
        $tid = session('username');


        // If session hasn't been started yet, start it
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // If the username is not in Laravel session, try to get it from PHP session
        if (!$tid && isset($_SESSION['username'])) {
            $tid = $_SESSION['username'];
            // Store it in Laravel session for future use
            session(['username' => $tid]);
        }

        // Get the school's UDISE code for the teacher
        $teacher = DB::table('sms.tch_profile')
            ->where('slno', $tid)
            ->where('tch_status', 'W')
            ->first();

        $ucode = $teacher->udise_sch_code ?? null;

        // If UDISE code is available, get school and student data
        if ($ucode) {
            // Get school information with related data
            $schoolInfo = DB::table('mizoram115.school_master as a')
                ->join('mizoram115.mst_district as b', 'a.district_cd', '=', 'b.udise_district_code')
                ->join('mizoram115.mst_block as c', 'a.block_cd', '=', 'c.udise_block_code')
                ->select(
                    'a.district_cd',
                    'c.udise_block_code',
                    'b.district_name',
                    'a.sch_category_id as schcat',
                    'a.sch_mgmt_id as schmgmt',
                    'a.sch_type',
                    'c.block_name',
                    'a.school_name',
                    'a.udise_sch_code'
                )
                ->where('a.udise_sch_code', $ucode)
                ->first();

            // Get student list - Fix PostgreSQL casting issues with section_id and stud_status
            $students = DB::table('sms.student_profile as a')
                ->select(
                    'a.*',
                    DB::raw("CASE
                        WHEN a.pres_class = -1 THEN 'Class UKG/KG2/PP1'
                        WHEN a.pres_class = -2 THEN 'Class LKG/KG1/PP2'
                        WHEN a.pres_class = -3 THEN 'Class Nursery/KG/PP3'
                        WHEN a.pres_class = 1 THEN 'Class 1'
                        WHEN a.pres_class = 2 THEN 'Class 2'
                        WHEN a.pres_class = 3 THEN 'Class 3'
                        WHEN a.pres_class = 4 THEN 'Class 4'
                        WHEN a.pres_class = 5 THEN 'Class 5'
                        WHEN a.pres_class = 6 THEN 'Class 6'
                        WHEN a.pres_class = 7 THEN 'Class 7'
                        WHEN a.pres_class = 8 THEN 'Class 8'
                        WHEN a.pres_class = 9 THEN 'Class 9'
                        WHEN a.pres_class = 10 THEN 'Class 10'
                        WHEN a.pres_class = 11 THEN 'Class 11'
                        WHEN a.pres_class = 12 THEN 'Class 12'
                        ELSE 'Unknown'
                    END as clsid"),
                    DB::raw("CASE
                        WHEN gender = '1' THEN 'Male'
                        WHEN gender = '2' THEN 'Female'
                        WHEN gender = '3' THEN 'Trans.'
                        ELSE gender
                    END as gender"),
                    DB::raw("CASE
                        WHEN section_id::text = '1' THEN 'Section A'
                        WHEN section_id::text = '2' THEN 'Section B'
                        WHEN section_id::text = '3' THEN 'Section C'
                        WHEN section_id::text = '4' THEN 'Section D'
                        WHEN section_id::text = '5' THEN 'Section E'
                        WHEN section_id::text = '6' THEN 'Section F'
                        ELSE section_id::text
                    END as section_name"),
                    DB::raw("CASE
                        WHEN stud_status = 'E' THEN 'Enrolled'
                        WHEN stud_status = 'P' THEN 'Pending'
                        ELSE stud_status
                    END as stud_Status")
                )
                ->where('a.udise_cd', $ucode)
                ->whereIn('a.stud_status', ['E', 'P'])
                ->orderBy('a.pres_class')
                ->orderBy('a.student_name')
                ->get();

            return view('students.list', compact('students', 'schoolInfo'));
        }

        // If no UDISE code is found
        return view('students.list', ['students' => [], 'schoolInfo' => null]);
    }

    public function exportExcel()
    {
        // Get the username from session
        $tid = session('username');

        // If session hasn't been started yet, start it
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // If the username is not in Laravel session, try to get it from PHP session
        if (!$tid && isset($_SESSION['username'])) {
            $tid = $_SESSION['username'];
        }

        // Get the school's UDISE code for the teacher
        $ucode = DB::table('sms.tch_profile')
            ->where('slno', $tid)
            ->where('tch_status', 'W')
            ->value('udise_sch_code');

        if (!$ucode) {
            return redirect()->route('students.index')->with('error', 'School not found');
        }

        // Get school info for the filename
        $schoolName = DB::table('mizoram115.school_master')
            ->where('udise_sch_code', $ucode)
            ->value('school_name');

        // Clean up the school name for use in a filename
        $schoolSlug = str_replace([' ', ',', '.'], ['_', '', ''], strtolower($schoolName ?? 'school'));
        $fileName = $schoolSlug . '_students_' . date('Y-m-d') . '.xlsx';

        // Use the Laravel Excel package to create and download the Excel file
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\StudentsExport($ucode),
            $fileName
        );
    }
}
