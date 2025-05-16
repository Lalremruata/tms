<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\student_profile;
use App\Models\SchoolMaster;
use Illuminate\Validation\Rule;
use DateTime;

class StudentAddController extends Controller
{
    /**
     * Get the teacher ID from session
     */
    private function getTeacherId()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return session('username');
    }

    /**
     * Get school information based on teacher ID
     */
    private function getSchoolInfo($teacherId)
    {
        $schoolInfo = DB::table('sms.tch_profile')
            ->where('slno', $teacherId)
            ->where('tch_status', 'W')
            ->value('udise_sch_code');

        if (empty($schoolInfo)) {
            return null;
        }

        $schoolDetails = DB::table('mizoram115.school_master as a')
            ->join('mizoram115.mst_district as b', 'a.district_cd', '=', 'b.udise_district_code')
            ->join('mizoram115.mst_block as c', 'a.block_cd', '=', 'c.udise_block_code')
            ->select(
                'a.school_name as schnm',
                'a.district_cd as dstcd',
                'a.block_cd as blkcd',
                'b.district_name as dstname',
                'c.block_name as blkname',
                'a.sch_category_id as schcat',
                'a.sch_type as schtyp',
                'a.sch_mgmt_id as schmgmt',
                'a.udise_sch_code as ucode'
            )
            ->where('a.udise_sch_code', $schoolInfo)
            ->first();

        return $schoolDetails;
    }

    /**
     * Get class options based on school category
     */
    private function getClassOptions($schoolCategory)
    {
        return DB::table('sms.class_desc')
            ->select('clsid', 'cls_desc')
            ->where('cat', $schoolCategory)
            ->orderBy('cls_desc')
            ->get();
    }

    /**
     * Show the student addition form
     */
    public function index()
    {
        $teacherId = $this->getTeacherId();
        $schoolInfo = $this->getSchoolInfo($teacherId);

        if (!$schoolInfo) {
            return redirect()->route('dashboard')
                ->with('error', 'Only Teacher from This School can Add the Students data, Please Select another Teacher...!!!');
        }

        $classOptions = $this->getClassOptions($schoolInfo->schcat);

        return view('students.student-add', compact('schoolInfo', 'classOptions'));
    }

    /**
     * Store a new student
     */
    public function store(Request $request)
    {
        // Validate request data
        $validated = $request->validate([
            'nattchcode' => 'nullable|string|max:20',
            'tchname' => 'required|string|max:100',
            'se_x' => 'required|in:1,2',
            'dob' => 'required|date_format:d-m-Y',
            'prcls' => 'required|string',
            'sec' => 'required|in:1,2,3,4,5,6,7,8',
            'fthname' => 'required|string|max:100',
            'mthname' => 'required|string|max:100',
            'guardname' => 'nullable|string|max:100',
            'addr' => 'required|string|max:255',
            'pcode' => 'nullable|digits:6',
            'mob' => 'nullable|digits:10',
            'eml' => 'nullable|email|max:100',
            'cat' => 'required|in:1,2,3,4',
            'cws' => 'required|in:1,2',
            'aadhaar' => 'nullable|digits:12',
            'apaar' => 'nullable|string|max:20',
            'nationality' => 'required|in:Indian,Foreigner',
            'remark' => 'nullable|string|max:255',
            // Hidden fields
            'dstcd' => 'required',
            'blkcd' => 'required',
            'schnm' => 'required',
            'ucode' => 'required',
            'schcat' => 'required',
            'schmgt' => 'required',
        ]);

        // Check student age (at least 3 years old)
        $birthDate = DateTime::createFromFormat('d-m-Y', $validated['dob']);
        $today = new DateTime();
        $age = $birthDate->diff($today);

        if ($age->y < 3) {
            return back()->withInput()
                ->with('error', 'Date of Birth is not correct, The Student Age must be equal to or more than 3 Years.... Record not saved!!!');
        }

        // Check if student already exists with same name, father name and DOB
        $existingStudent = DB::table('sms.student_profile')
            ->whereRaw('UPPER(TRIM(student_name)) = ?', [strtoupper(trim($validated['tchname']))])
            ->whereRaw('UPPER(TRIM(father_name)) = ?', [strtoupper(trim($validated['fthname']))])
            ->whereRaw('TRIM(student_dob) = ?', [trim($validated['dob'])])
            ->exists();

        if ($existingStudent) {
            return back()->withInput()
                ->with('error', 'This Student Name is already exists, use the Transfer from Another School Facility or the DropBox Facility to get the Student in your school!!!');
        }

        // If student PEN is provided, check if it exists
        if (!empty($validated['nattchcode'])) {
            $existingPEN = DB::table('sms.student_profile')
                ->where('student_pen', trim($validated['nattchcode']))
                ->exists();

            if ($existingPEN) {
                return back()->withInput()
                    ->with('error', 'This Student PEN was already there kindly use the Transfer from Another School Facility or the DropBox Facility to get the Student in your school!!!');
            }
        } else {
            // Generate a new unique student PEN
            $maxUserId = DB::table('sms.student_profile')->max('userid') ?? 0;
            $newUserId = $maxUserId + 1;

            do {
                $studentPen = 'VSK' . $newUserId;
                $penExists = DB::table('sms.student_profile')
                    ->where('student_pen', $studentPen)
                    ->exists();

                if ($penExists) {
                    $newUserId++;
                }
            } while ($penExists);

            $validated['nattchcode'] = $studentPen;
        }

        // Set default values
        $mobile = empty($validated['mob']) ? '0000000000' : $validated['mob'];
        $lastUpdateDate = Carbon::now();

        // Insert new student
        try {
            DB::table('sms.student_profile')->insert([
                'udise_cd' => $validated['ucode'],
                'school_name' => $validated['schnm'],
                'district_cd' => $validated['dstcd'],
                'block_cd' => $validated['blkcd'],
                'sch_category_id' => $validated['schcat'],
                'sch_mgmt_id' => $validated['schmgt'],
                'student_pen' => $validated['nattchcode'],
                'student_name' => $validated['tchname'],
                'gender' => $validated['se_x'],
                'student_dob' => $validated['dob'],
                'class_id' => $validated['prcls'],
                'section_id' => $validated['sec'],
                'mother_name' => $validated['mthname'],
                'father_name' => $validated['fthname'],
                'guardian_name' => $validated['guardname'],
                'address' => $validated['addr'],
                'pincode' => $validated['pcode'],
                'mobile_no_1' => $mobile,
                'mobile_no_2' => 0,
                'email_id' => $validated['eml'],
                'mother_tongue' => 0,
                'soc_cat_id' => $validated['cat'],
                'minority_id' => 0,
                'is_bpl_yn' => 2,
                'aay_bpl_yn' => 2,
                'ews_yn' => 2,
                'cwsn_yn' => $validated['cws'],
                'nat_ind_yn' => 2,
                'oosc_yn' => 2,
                'oosc_mainstreamed_yn' => 9,
                'pres_class' => $validated['prcls'],
                'stud_status' => 'P',
                'last_upd_date' => $lastUpdateDate,
                'aadhaar_no' => $validated['aadhaar'] ?: null,
                'apaar_id' => $validated['apaar'] ?: null,
                'nationality' => $validated['nationality'],
                'remark' => $validated['remark'],
            ]);

            return redirect()->route('students.add')
                ->with('success', 'Data Saved Successfully!!!!!');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'An error occurred while saving student data: ' . $e->getMessage());
        }
    }
}
