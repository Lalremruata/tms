<?php

namespace App\Http\Controllers\CustomAuth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        return view('auth.clogin');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'mobile_no' => ['required', 'string'],
            'trade' => ['required', 'string'],
        ]);

        // Attempt to find the user by mobile_no and trade
        $user = tch_profile::where('mobile_no', $request->mobile_no)
                            ->where('dob', $request->trade)
                            ->first();

        // If user not found, throw validation exception
        if (!$user) {
            throw ValidationException::withMessages([
                'mobile_no' => trans('auth.failed'),
            ]);
        }

        // Attempt to login the user
        Auth::login($user, $request->boolean('remember'));

        // Check if authentication was successful
        if (Auth::check()) {

            // Regenerate session to prevent session fixation attacks
            $request->session()->regenerate();

            // Redirect to intended page after successful login
            return redirect()->intended('/dashboard');
        }

        // Handle failed authentication (this block should not be reached if Auth::login() is successful)
        return back()->withErrors([
            'mobile_no' => trans('auth.failed'),
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

        return redirect('/');
    }
}
