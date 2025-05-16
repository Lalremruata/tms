<?php

namespace App\Http\Controllers;

use App\Models\Schoolmaster;
use App\Models\Std_profile;
use App\Models\Student;
use App\Models\tch_data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\UpdateStudentRequest;

class StudentCorrectionController extends Controller
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

        return $tid = session('username');
    }

    /**
     * Get the school's UDISE code for the teacher
     *
     * @param string $teacherId
     * @return string|null
     */
    private function getSchoolUdiseCode($teacherId)
    {
        $teacher = DB::table('sms.tch_profile')
            ->where('slno', $teacherId)
            ->where('tch_status', 'W')
            ->first();

        return $teacher->udise_sch_code ?? null;
    }

    /**
     * Display students for correction based on class and section
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        // Get the teacher ID from session
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
    
        if (!$tid) {
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }
    
        // Get the school's UDISE code for the teacher
        $udise = $this->getSchoolUdiseCode($tid);
    
        if (!$udise) {
            $user = Auth::user();
            $tch_data = tch_data::where('id', $user->id)->with('tch_profile')->first();
            $std_profile = $tch_data->tch_profile->first();
            $std_profile = Std_profile::where('udise_cd', $std_profile->udise_sch_code)
                ->select('pres_class', 'udise_cd')->distinct()->get();
    
            $schooldata = $tch_data->tch_profile->first();
            $schooldata = Schoolmaster::where('udise_sch_code', $schooldata->udise_sch_code)->get();
    
            $tch_profile = tch_data::where('slno', $user->slno)->with('tch_profile')->first();
    
            return view('vsk.frontend.dashboard', compact('user', 'tch_data', 'tch_profile', 'schooldata', 'std_profile'))
                ->with('error', 'You are not assigned to any school.');
        }
    
        // Initialize variables
        $selectedClass = $request->input('parameter2', null);
        $selectedSection = $request->input('sec', null);
        $searchTerm = $request->input('search', null); // Get search term from request
    
        try {
            // Build the base query for students
            $query = DB::table('sms.student_profile as a')
                ->select(
                    'a.udise_cd',
                    'a.student_pen',
                    'a.soc_cat_id',
                    'a.cwsn_yn',
                    'a.student_name',
                    'a.gender',
                    DB::raw('(select c.school_name from mizoram115.school_master c where c.udise_sch_code = a.udise_cd) as schname'),
                    'a.student_dob',
                    'a.father_name',
                    'a.mother_name',
                    'a.mobile_no_1',
                    'a.section_id',
                    'a.class_id',
                    'a.aadhaar_no',
                    'a.apaar_id',
                    'a.nationality',
                    'a.remark',
                    DB::raw("(case
                    when a.class_id = -1 then 'Class UKG/KG2/PP1'
                    when a.class_id = -2 then 'Class LKG/KG1/PP2'
                    when a.class_id = -3 then 'Class Nursery/KG/PP3'
                    when a.class_id = 1 then 'Class 1'
                    when a.class_id = 2 then 'Class 2'
                    when a.class_id = 3 then 'Class 3'
                    when a.class_id = 4 then 'Class 4'
                    when a.class_id = 5 then 'Class 5'
                    when a.class_id = 6 then 'Class 6'
                    when a.class_id = 7 then 'Class 7'
                    when a.class_id = 8 then 'Class 8'
                    when a.class_id = 9 then 'Class 9'
                    when a.class_id = 10 then 'Class 10'
                    when a.class_id = 11 then 'Class 11'
                    when a.class_id = 12 then 'Class 12'
                    end) as clsid"),
                    'a.pres_class',
                    'a.stud_status'
                )
                ->where('a.udise_cd', $udise)
                ->whereIn('a.stud_status', ['P', 'E']);
    
            // Apply search filter if provided
            if ($searchTerm) {
                $query->where(function($q) use ($searchTerm) {
                    $q->where('a.student_name', 'ILIKE', "%{$searchTerm}%")
                      ->orWhere('a.student_pen', 'ILIKE', "%{$searchTerm}%")
                      ->orWhere('a.father_name', 'ILIKE', "%{$searchTerm}%")
                      ->orWhere('a.mother_name', 'ILIKE', "%{$searchTerm}%");
                });
            }
    
            // Apply class filter if selected
            if ($selectedClass !== null) {
                $query->where('pres_class', (int)$selectedClass);
            }
    
            // Apply section filter if selected and not "All Sections"
            if ($selectedSection !== null && $selectedSection != '0') {
                $query->where('a.section_id', $selectedSection);
    
                // Add count of students per section
                $query->addSelect(DB::raw('(select count(*) from sms.student_profile where udise_cd = a.udise_cd and pres_class = a.pres_class and section_id = a.section_id) as nos'));
            } else {
                // Add count of students per class (for all sections or no filter)
                $query->addSelect(DB::raw('(select count(*) from sms.student_profile where udise_cd = a.udise_cd and pres_class = a.pres_class) as nos'));
            }
    
            // Sort by class and then by student name
            $query->orderBy('a.pres_class')->orderBy('a.student_name');
    
            // Use pagination for 50 students per page
            // The appends() method preserves query parameters in pagination links
            $students = $query->paginate(50)->appends($request->except('page'));
    
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while fetching student data: ' . $e->getMessage());
        }
    
        // Fetch dropdown options for class and section
        $classOptions = $this->getClassOptions($udise);
        $sectionOptions = $this->getSectionOptions();
    
        return view('students.correction', compact(
            'students',
            'classOptions',
            'sectionOptions',
            'selectedClass',
            'selectedSection',
            'searchTerm' // Pass search term to the view
        ));
    }

    /**
     * Get class options for dropdown
     *
     * @param string $udise
     * @return \Illuminate\Support\Collection
     */
    private function getClassOptions($udise)
    {
        return DB::table('sms.student_profile')
            ->select(
                'pres_class',
                DB::raw("CASE
                    WHEN pres_class = -1 THEN 'Class UKG/KG2/PP1'
                    WHEN pres_class = -2 THEN 'Class LKG/KG1/PP2'
                    WHEN pres_class = -3 THEN 'Class Nursery/KG/PP3'
                    WHEN pres_class = 1 THEN 'Class 1'
                    WHEN pres_class = 2 THEN 'Class 2'
                    WHEN pres_class = 3 THEN 'Class 3'
                    WHEN pres_class = 4 THEN 'Class 4'
                    WHEN pres_class = 5 THEN 'Class 5'
                    WHEN pres_class = 6 THEN 'Class 6'
                    WHEN pres_class = 7 THEN 'Class 7'
                    WHEN pres_class = 8 THEN 'Class 8'
                    WHEN pres_class = 9 THEN 'Class 9'
                    WHEN pres_class = 10 THEN 'Class 10'
                    WHEN pres_class = 11 THEN 'Class 11'
                    WHEN pres_class = 12 THEN 'Class 12'
                END AS cls")
            )
            ->where('udise_cd', $udise)
            ->distinct()
            ->orderBy('pres_class')
            ->get();
    }

    /**
     * Get section options for dropdown
     *
     * @return \Illuminate\Support\Collection
     */
    private function getSectionOptions()
    {
        return DB::table('sms.student_profile')
            ->select(
                'section_id',
                DB::raw("CASE
                    WHEN section_id = 1 THEN 'Section A'
                    WHEN section_id = 2 THEN 'Section B'
                    WHEN section_id = 3 THEN 'Section C'
                    WHEN section_id = 4 THEN 'Section D'
                    WHEN section_id = 5 THEN 'Section E'
                    WHEN section_id = 6 THEN 'Section F'
                    WHEN section_id = 7 THEN 'Section G'
                    WHEN section_id = 8 THEN 'Section H'
                END AS sec")
            )
            ->distinct()
            ->union(DB::table(DB::raw("(SELECT 0 as section_id, 'All Sections' as sec) as temp")))
            ->orderBy('sec')
            ->get();
    }


    /**
     * Show form for editing a specific student
     *
     * @param string $studentPen
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($studentPen)
    {
        // Get the teacher ID from session
        $tid = $this->getTeacherId();

        if (!$tid) {
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        // Get the school's UDISE code for the teacher
        $udise = $this->getSchoolUdiseCode($tid);

        if (!$udise) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not assigned to any school.');
        }

        // Fetch the student record
        $student = DB::table('sms.student_profile')
            ->select(
                'student_pen',
                'student_name',
                'father_name',
                'mother_name',
                'gender',
                'student_dob',
                'mobile_no_1',
                'cwsn_yn',
                'pres_class',
                'section_id',
                'stud_status',
                'soc_cat_id',
                'aadhaar_no',
                'apaar_id',
                'nationality',
                'remark'
            )
            ->where('student_pen', $studentPen)
            ->where('udise_cd', $udise)
            ->first();

        if (!$student) {
            return redirect()->route('students.correction')
                ->with('error', 'Student not found or you do not have permission to edit this student.');
        }

        // Get dropdown options for class and section
        $classOptions = $this->getClassOptions($udise);
        $sectionOptions = $this->getSectionOptions();

        return view('students.edit', compact(
            'student',
            'classOptions',
            'sectionOptions'
        ));
    }

    public function update(Request $request)
    {
        $studentPen = $request->input('student_pen');
        // Get the teacher ID from session
        $tid = $this->getTeacherId();

        if (!$tid) {
            return redirect()->route('login')
                ->with('error', 'Session expired. Please login again.');
        }

        // Get the school's UDISE code for the teacher
        $udise = $this->getSchoolUdiseCode($tid);

        if (!$udise) {
            return redirect()->route('dashboard')
                ->with('error', 'Only teachers from this school can update student data.');
        }

        try {
            // Validate request
            $validated = $request->validate([
                'student_pen' => 'required|string',
                'student_name' => 'required|string|max:100',
                'father_name' => 'nullable|string|max:100',
                'mother_name' => 'nullable|string|max:100',
                'gender' => 'required|in:1,2',
                'dob' => 'required|date',
                'mobile' => 'nullable|string|max:10',
                'class' => 'required',
                'section' => 'required',
                'status' => 'required|in:E,W,T,A,D,P,O',
                'category' => 'required|in:1,2,3,4,5',
                'cwsn' => 'required|in:1,2',
                'aadhaar' => 'nullable|string|max:12',
                'apaar' => 'nullable|string|max:50',
                'nationality' => 'nullable|string|max:50',
                'remark' => 'nullable|string|max:255'
            ]);

            // Process aadhaar and apaar (set to null if empty)
            $aadhaar = !empty($validated['aadhaar']) ? $validated['aadhaar'] : null;
            $apaar = !empty($validated['apaar']) ? $validated['apaar'] : null;
            $remark = !empty($validated['remark']) ? $validated['remark'] : null;

            // Update student record
            $updated = DB::table('sms.student_profile')
                ->where('student_pen', $studentPen)
                ->update([
                    'gender' => $validated['gender'],
                    'soc_cat_id' => $validated['category'],
                    'cwsn_yn' => $validated['cwsn'],
                    'student_name' => $validated['student_name'],
                    'father_name' => $validated['father_name'],
                    'mother_name' => $validated['mother_name'],
                    'last_upd_date' => now(),
                    'student_dob' => $validated['dob'],
                    'mobile_no_1' => $validated['mobile'],
                    'pres_class' => (int)$validated['class'],
                    'class_id' => (int)$validated['class'],
                    'section_id' => (int)$validated['section'],
                    'stud_status' => $validated['status'],
                    'aadhaar_no' => $aadhaar,
                    'apaar_id' => $apaar,
                    'nationality' => $validated['nationality'],
                    'remark' => $remark
                ]);

            if ($updated) {
                return redirect()->route('students.correction')
                    ->with('success', "Student {$validated['student_name']} updated successfully!");
            }

            return redirect()->route('students.correction')
                ->with('error', 'No changes were made to the student record.');

        } catch (\Exception $e) {

            return redirect()->route('students.correction')
                ->with('error', 'Error updating student: ' . $e->getMessage());
        }
    }

    public function updateInline(Request $request)
    {
        // Get the student PEN from the request
        $studentPen = $request->input('student_pen');
        
        // Get the teacher ID from session
        $tid = $this->getTeacherId();

        if (!$tid) {
            return redirect()->route('login')
                ->with('error', 'Session expired. Please login again.');
        }

        // Get the school's UDISE code for the teacher
        $udise = $this->getSchoolUdiseCode($tid);

        if (!$udise) {
            return redirect()->route('dashboard')
                ->with('error', 'Only teachers from this school can update student data.');
        }

        try {
            // Validate request with clear error messages
            $validated = $request->validate([
                'student_pen' => 'required|string',
                'student_name' => 'required|string|max:100',
                'status' => 'required|in:E,W,T,A,D,P,O',
                'category' => 'required|in:1,2,3,4,5',
                'aadhaar' => 'nullable|string|max:12',
                'apaar' => 'nullable|string|max:50',
                'nationality' => 'nullable|string|max:50',
                'remark' => 'nullable|string|max:255'
            ], [
                'student_name.required' => 'The student name field is required.',
                'status.required' => 'The status field is required.',
                'category.required' => 'The category field is required.'
            ]);

            // Update student record
            $updated = DB::table('sms.student_profile')
                ->where('student_pen', $validated['student_pen'])
                ->update([
                    'student_name' => $validated['student_name'],
                    'stud_status' => $validated['status'],
                    'soc_cat_id' => $validated['category'],
                    'aadhaar_no' => !empty($validated['aadhaar']) ? $validated['aadhaar'] : null,
                    'apaar_id' => !empty($validated['apaar']) ? $validated['apaar'] : null,
                    'nationality' => $validated['nationality'],
                    'remark' => !empty($validated['remark']) ? $validated['remark'] : null,
                ]);

            if ($updated) {
                
                return redirect()->back()
                    ->with('success', "Student {$validated['student_name']} updated successfully!");
            }

            return redirect()->back()
                ->with('error', 'No changes were made to the student record.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating student: ' . $e->getMessage());
        }
    }
}
