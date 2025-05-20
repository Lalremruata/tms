<?php

namespace App\Http\Controllers;

use App\Models\Schoolmaster;
use App\Models\Std_profile;
use App\Models\tch_data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Teacher;
use App\Models\School;
use App\Models\Student;
use App\Exports\StudentsExport;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    public function index(){
        $user=Auth::user();
        $tch_data=tch_data::where('id',$user->id)->with('tch_profile')->first();
        $std_profile= $tch_data->tch_profile->first();
        // return $std_profile->udise_sch_code;
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
            ->where('a.udise_sch_code', $std_profile->udise_sch_code)
            ->first();
        // Count by class
        $std_by_class = Std_profile::where('udise_cd', $std_profile->udise_sch_code)
            ->where('stud_status', 'E')
            ->select('udise_cd','class_id', 'section_id', DB::raw('count(*) as student_count'))
            ->groupBy('class_id', 'section_id', 'udise_cd')
            ->orderBy('class_id')
            ->orderBy('section_id')
            ->get();

        // Get total count
        $total_students = Std_profile::where('udise_cd', $std_profile->udise_sch_code)
            ->where('stud_status', 'E')
            ->count();

        $schooldata= $tch_data->tch_profile->first();
        $schooldata = Schoolmaster::where('udise_sch_code',$schooldata->udise_sch_code)->get();
        // return $schooldata;

        // $schoollist=Schoolmaster::select('udise_sch_code', 'school_name')->get();


        return view('students.class-list',compact('user','tch_data', 'schooldata','std_by_class', 'schoolInfo','total_students'));
    }
    public function allStudents()
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
                ->paginate(50);

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

        // Clean up the school name for use in a filename, ensuring all invalid characters are removed
        $schoolSlug = $this->sanitizeFilename($schoolName ?? 'school');
        $fileName = $schoolSlug . '_students_' . date('Y-m-d') . '.xlsx';

        // Use the Laravel Excel package to create and download the Excel file
        return Excel::download(
            new StudentsExport($ucode),
            $fileName
        );
    }

    public function exportExcelByClassSection($udiseCode, $classId, $sectionId)
    {
        // Validate the parameters
        if (empty($udiseCode) || $classId === null || $sectionId === null) {
            return redirect()->back()->with('error', 'Invalid parameters provided');
        }

        // Get school info for the filename
        $schoolName = DB::table('mizoram115.school_master')
            ->where('udise_sch_code', $udiseCode)
            ->value('school_name');

        // Get class name and sanitize it
        $className = $this->getClassName($classId);
        $classNameSafe = $this->sanitizeFilename($className);

        // Get section name
        $sectionName = chr(64 + (int)$sectionId); // 1 -> A, 2 -> B, etc.

        // Clean up the school name for use in a filename
        $schoolSlug = $this->sanitizeFilename($schoolName ?? 'school');
        $fileName = $schoolSlug . '_class_' . $classNameSafe . '_section_' . $sectionName . '_' . date('Y-m-d') . '.xlsx';

        // Use the Laravel Excel package to create and download the Excel file
        return Excel::download(
            new StudentsExport($udiseCode, $classId, $sectionId),
            $fileName
        );
    }

    /**
     * Sanitize a string to make it safe for use in filenames
     *
     * @param string $string The string to sanitize
     * @return string The sanitized string
     */

    public function getStudentsByClassSection($udise_cd, $class_id, $section_id)
    {
        $students = Std_profile::where('udise_cd', $udise_cd)
            ->where('class_id', $class_id)
            ->where('section_id', $section_id)
            ->where('stud_status', 'E')
            ->get();

        $className = $this->getClassName($class_id);
        $sectionName = $this->getSectionName($section_id);

        return view('students.by-class-section', [
            'students' => $students,
            'class_name' => $className,
            'section_name' => $sectionName,
            'udise_cd' => $udise_cd,
            'class_id' => $class_id,
            'section_id' => $section_id
        ]);
    }
    private function sanitizeFilename($string)
    {
        // Remove any characters that are invalid in filenames
        $string = preg_replace('/[\/\?<>\\:\*\|"]/', '', $string);
        // Replace spaces with underscores
        $string = str_replace(' ', '_', $string);
        // Remove any other potentially problematic characters
        $string = preg_replace('/[^A-Za-z0-9_\-\.]/', '', $string);
        // Ensure filename isn't too long
        $string = substr($string, 0, 100);

        return $string;
    }

    private function getClassName($class_id)
    {
        $classNames = [
            '-3' => 'Nursery/KG/PP3',
            '-2' => 'LKG/KG1/PP2',
            '-1' => 'UKG/KG2/PP1',
            '1' => '1',
            '2' => '2',
            '3' => '3',
            '4' => '4',
            '5' => '5',
            '6' => '6',
            '7' => '7',
            '8' => '8',
            '9' => '9',
            '10' => '10',
            '11' => '11',
            '12' => '12'
        ];

        return $classNames[$class_id] ?? $class_id;
    }
    private function getSectionName($section_id)
    {
        // Convert numeric section_id to letter (1 -> A, 2 -> B, etc.)
        if ($section_id >= 1 && $section_id <= 26) {
            return chr(64 + $section_id); // ASCII: 65 = 'A', so 1+64 = 65 = 'A'
        }
        return $section_id;
    }
}
