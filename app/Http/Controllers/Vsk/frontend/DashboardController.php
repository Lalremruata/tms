<?php

namespace App\Http\Controllers\Vsk\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\tch_data;
use App\Models\tch_profile;
use App\Models\tch_profile_update;
use App\Models\Schoolmaster;
use App\Models\student_profile;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Std_profile;
use App\Models\student_attendance;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function index()
    {
        $user=Auth::user();
        $tch_data=tch_data::where('id',$user->id)->with('tch_profile')->first();
        $std_profile= $tch_data->tch_profile->first();
        // return $std_profile->udise_sch_code;
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

        $tch_profile=tch_data::where('slno',$user->slno)->with('tch_profile')->first();
        // $sessionId = session()->getId();



        return view('vsk.frontend.dashboard',compact('user','tch_data','tch_profile','schooldata','std_by_class', 'total_students')); // Pass the user to the view
    }
    public function openstudentattendance()
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

        return view('vsk.frontend.openstudentattendance',compact('user','tch_data','tch_profile','schooldata','std_profile','std_profile1')); // Pass the user to the view
    }
    public function vskprofile()
    {
        $user=Auth::user();
        $_SESSION['username'] = $user->slno;

        $user=tch_data::where('id',$user->id)->with('tch_profile')->first();
        $user= $user->tch_profile->first();
        return view('vsk.frontend.profile',compact('user'));
    }

    public function updateProfile(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'mobile_no' => 'required|string|max:15',
            'email' => 'required|email|max:255',
            'dob' => 'required|date',
        ]);


        $user = Auth::user();
        $userProfile = tch_data::find($user->id); // Assuming `id` is the primary key
        $_SESSION['username'] = $user->slno;
        $userProfile=tch_profile_update::find($userProfile->slno);
        $userProfile->tch_name = $request->input('name');
        $userProfile->mobile_no = $request->input('mobile_no');
        $userProfile->ema_il = $request->input('email');
        $userProfile->dob = $request->input('dob');
        $userProfile->save();
        return redirect()->back()->with('message', 'Profile updated successfully.');
    }

    public function changepassword()
    {
        $user=Auth::user();
        $_SESSION['username'] = $user->slno;
        return view('vsk.frontend.changepassword',compact('user'));
    }

    public function loginchangepassword()
    {
        $user=Auth::user();
        $_SESSION['username'] = $user->slno;
        return view('vsk.frontend.loginchangepassword',compact('user'));
    }

    public function changepasswordpost(Request $request)
    {
        $request->validate([
            'new_password' => 'required|string|max:15|',
            'new_password_confirmation'=> 'required',
        ]);

        if($request->log=='2')
        {

            if ($request->current_password)
            {
                $check = $request->current_password;

                if(Auth::user()->passw!==$check)
                {
                    return redirect()->back()->with(['message'=>'Current Password does not Match!']);
                }
                else
                {

                    $user = Auth::user();
                    $_SESSION['username'] = $user->slno;
                    $userProfile = tch_data::find($user->id); // Assuming `id` is the primary key
                    $userProfile->passw = $request->input('new_password');
                    $userProfile->save();
                    return redirect()->back()->with(['message' => 'Password updated successfully.', 'updateSuccess' => true]);
                }
            }
        }



        $user = Auth::user();
        $userProfile = tch_data::find($user->id); // Assuming `id` is the primary key
        $_SESSION['username'] = $user->slno;

        // Update the new password without hashing
        $userProfile->passw = $request->input('new_password');
        $userProfile->init = '1';
        $userProfile->sections = $request->input('section');
        $userProfile->classes = $request->input('classes');


        // Save the updated password to the database
        $userProfile->save();

        if($request->log ==1)
        {
            return redirect()->route('vskdashboard');
        }

        return redirect()->back()->with(['message' => 'Password updated successfully.', 'updateSuccess' => true]);
    }

    public function stddb (){
        $profiles= student_profile::take(1)->with('getdistrict')->get();
        // $profiles->getschool();
        return $profiles;
        return view('vsk.frontend.stddb', compact('profiles'));
    }

    // public function getProfiles(Request $request)
    // {
    //     if ($request->ajax()) {
    //         $data = student_profile::query();

    //         if ($request->has('name') && $request->name != '') {
    //             $data->where('name', 'like', "%{$request->name}%");
    //         }

    //         if ($request->has('status') && $request->status != '') {
    //             $data->where('stud_status', $request->status);
    //         }

    //         return DataTables::of($data)
    //             ->addIndexColumn()
    //             ->make(true);
    //     }


    //     return view('vsk.frontend.stddbb');
    // }


    // public function getProfiles(Request $request)
    // {
    //     if ($request->ajax()) {
    //         $data = student_profile::query();

    //         if ($request->has('name') && $request->name != '') {
    //             $data->where('student_name', 'like', "%{$request->name}%");
    //         }
    //         try {
    //             return DataTables::of($data)
    //                 ->addIndexColumn()
    //                 ->make(true);
    //         } catch (\Exception $e) {
    //             // Log the error
    //             \Log::error($e->getMessage());
    //             return response()->json(['error' => 'Error processing request'], 500);
    //         }
    //     }
    //     return view('vsk.frontend.stddbb');
    // }

    public function getProfiles(Request $request)
    {
        // Base query with eager loading
        $query = student_profile::with('getdistrict')->orderBy('student_name', 'asc');

        // Filter by district, name, and class_id if provided
        if ($request->has('district') && $request->district != '') {
            $query->where('district_cd', $request->district);
        }

        if ($request->has('name') && $request->name != '') {
            $name = strtoupper($request->name);
            $query->whereRaw('UPPER(student_name) LIKE ?', ['%' . $name . '%']);
        }

        if ($request->has('class_id') && $request->class_id != '') {
            $query->where('class_id', $request->class_id);
        }

        // Paginate results
        $profiles = $query->paginate(1000);

        return view('vsk.frontend.stddbb', compact('profiles'));
    }


      // MarkAttendance
    public function markAttendance(Request $request)
    {
        $user = Auth::user();
        $date = now()->toDateString();
        $time = now()->toTimeString();
        $ipAddress = $request->ip();
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        // Check if attendance already marked for today
        $attendance = Attendance::where('user_id', $user->id)
                                ->where('date', $date)
                                ->first();

        if ($attendance) {
            return response()->json(['message' => 'You have already marked attendance for today.'], 403);
        }

        Attendance::create([
            'user_id' => $user->id,
            'date' => $date,
            'time' => $time,
            'ip_address' => $ipAddress,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);

        // return redirect()->back()->with(['message' => 'Attendance submitted successfully.', 'updateSuccess' => true]);
        return response()->json(['message' => 'Attendance marked successfully.'], 200);
    }

    public function updateschool(Request $request)
    {

        $school = Schoolmaster::where('udise_sch_code', $request->udise)->first();

        if ($school) {
            // Update the fields you want to change
            $school->email = $request->email;
            $school->head_master_name = $request->headname;
            // Save the changes
            $school->save();
            return redirect()->back()->with(['message' => 'School Data updated Successfully', 'updateSuccess' => true]);
        } else {
            return redirect()->back()->with(['message' => 'Fail to update']);

        }
    }





}
