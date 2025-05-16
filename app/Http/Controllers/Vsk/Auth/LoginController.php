<?php

namespace App\Http\Controllers\Vsk\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\tch_data;
use App\Models\tch_profile;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        
        return view('vsk.auth.logins');
    }

    public function vsksignup()
    {
        return view('vsk.frontend.dashboard');
    }
    /**
     * Handle an incoming authentication request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
       
        $request->validate([
            'slno' => 'required',
            'password' => 'required',
        ]);


        $credentials = $request->only('slno', 'password');
        $remember_token = $request->has('remember');

        $user = tch_data::where('slno', $request->slno)->first();

        if($user){
            if($user->init == 0)
            {
                Auth::login($user);
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                  }
                  $_SESSION['username'] = $user->slno;
                 
                
                return redirect()->intended('loginchangepassword');
            }
            else
            {
                $user = tch_data::where('slno', $credentials['slno'])->where('passw', $credentials['password'])->first();
                if($user)
                {
                    Auth::login($user);
                    
                    if (session_status() == PHP_SESSION_NONE) {
                        session_start();
                      }
                      $_SESSION['username'] = $user->slno;
                      
                       
                    return redirect()->intended('vskdashboard');
                }
                else
                {
                    return back()->withErrors([
                        'slno' => 'The provided credentials do not match our records.',
                    ]);
                }
            }
        }

        if (is_null($user))
        {
            $user = tch_profile::where('slno', $credentials['slno'])->where('tch_status','W')->first();
            if($user)
            {
                $newdata = New tch_data;
                $newdata->slno = $request->slno;
                $newdata->save();

                $user = tch_data::where('slno', $credentials['slno'])->first();
            }
            if ($user) {
                Auth::login($user);
                $_SESSION['username'] = $user->slno;
                 
                if ($user->init == 0)
                {
                    return redirect()->intended('loginchangepassword');
                }

                return redirect()->intended('vskdashboard');
            }
        }


        return back()->withErrors([
            'slno' => 'The provided credentials do not match our records.',
        ]);


    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();


        // return redirect('/');
        return redirect()->route('vskloginget'); // Redirect to the desired route
    }
    public function forgetpassword(){
        return view('vsk.frontend.forgetpassword');
    }

    public function forgetpasswordpost(Request $request){
        // return $request->slno;

        $temp=tch_profile::where('slno',$request->slno)->first();
        if($temp){
            return view('vsk.frontend.forgetpassword2',compact('temp'));
        }
        return view('vsk.frontend.forgetpassword')->withErrors([
            'slno' => 'The provided credentials do not match our records.',
        ]);

    }

    public function getforgetpassword(Request $request){

        $temp=tch_profile::where('ema_il',$request->email)->where('dob',$request->dob)->first();
        if (!$temp) {
            return redirect()->back()->withErrors(['error' => 'Email or Date of Birth does not match our records.'])->withInput();
        }

        $password=tch_data::where('slno',$temp->slno)->first();
        if($password)
        {
            $passw=$password;
            return view('vsk.frontend.forgetpassword4',compact('passw'));
        }
        else
        {
            $passw= $temp->slno;
            return view('vsk.frontend.forgetpassword3',compact('passw'));
        }

    }

    public function signup(){
        return view ('vsk.auth.signup');
    }
}
