<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Vsk\Auth\LoginController;
use App\Http\Controllers\Vsk\frontend\DashboardController;
use App\Http\Middleware\CustomRedirectIfAuthenticated;
use App\Http\Middleware\CustomGuestRedirect;
use App\Http\Controllers\Vsk\frontend\AttendanceController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentCorrectionController;
use App\Http\Controllers\StudentStatusController;
use App\Http\Controllers\StudentDropboxController;
use App\Http\Controllers\StudentAddController;

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// AUTHENTICATION
Route::middleware([CustomGuestRedirect::class])->group(function () {
    Route::get('/', [LoginController::class, 'create'])->name('vskloginget');
    Route::get('/vsksignup', [LoginController::class, 'vsksignup'])->name('vsksignup');
    Route::post('vsklogin', [LoginController::class, 'store'])->name('vskloginpost');
    Route::get('forgetpassword', [LoginController::class, 'forgetpassword'])->name('forgetpassword');
    Route::post('forgetpasswordpost', [LoginController::class, 'forgetpasswordpost'])->name('forgetpasswordpost');
    Route::get('forgetpasswordpost', [LoginController::class, 'forgetpasswordpost'])->name('forgetpasswordpost');
    Route::post('getforgetpassword', [LoginController::class, 'getforgetpassword'])->name('getforgetpassword');
});
Route::post('vsklogout', [LoginController::class, 'destroy'])->name('vsklogout');


// FRONTEND
Route::middleware([CustomRedirectIfAuthenticated::class])->group(function () {
    Route::get('/vskdashboard', [DashboardController::class, 'index'])->name('vskdashboard');
    Route::get('/vskprofile', [DashboardController::class, 'vskprofile'])->name('vskprofile');
    Route::post('/updateprofile', [DashboardController::class, 'updateProfile'])->name('updateprofile');
    Route::get('/changepassword', [DashboardController::class, 'changepassword'])->name('changepassword');
    Route::post('/changepasswordpost', [DashboardController::class, 'changepasswordpost'])->name('changepasswordpost');
    Route::get('/loginchangepassword', [DashboardController::class, 'loginchangepassword'])->name('loginchangepassword');
    Route::get('/markAttendance', [DashboardController::class, 'markAttendance'])->name('markAttendance');

    Route::post('/updateschool', [DashboardController::class, 'updateschool'])->name('updateschool');

    // Student List
    Route::get('/stddb', [DashboardController::class, 'stddb'])->name('stddb');


    // Attendance
    Route::get('/dashboard', [AttendanceController::class, 'showDashboard'])->name('dashboard');
    Route::post('/dashboard', [AttendanceController::class, 'fetchAttendanceData'])->name('fetch.attendance');
    Route::post('/submitattendance', [AttendanceController::class, 'submitattendance'])->name('submitattendance');


    // Student Attendance
    Route::get('/stdclass/{id}/{udise}/{section_id}', [AttendanceController::class, 'stdclass'])->name('stdclass');
    Route::post('/submitatt', [AttendanceController::class, 'submitatt'])->name('submitatt');

    // mark Attendances
    Route::get('/openstudentattendance', [DashboardController::class, 'openstudentattendance'])->name('openstudentattendance');


    Route::get('/generatereport/{id}/{udise}/{section_id}', [AttendanceController::class, 'generatereport'])->name('generatereport');
    Route::post('/generatereportdate', [AttendanceController::class, 'generatereportdate'])->name('generatereportdate');
});

// Student List Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::get('/students/export', [StudentController::class, 'exportExcel'])->name('students.export');

    Route::get('/students/add', [StudentAddController::class, 'index'])->name('students.add');

    // Store a new student
    Route::post('/students/store', [StudentAddController::class, 'store'])->name('students.store');

    // Student Correction Routes
    Route::get('/students/correction', [StudentCorrectionController::class, 'index'])->name('students.correction');
    Route::post('/students/correction', [StudentCorrectionController::class, 'index'])->name('students.correction.filter');
    Route::post('/students/update', [StudentCorrectionController::class, 'update'])->name('students.update');
    // Add the edit route
    Route::get('/students/{studentPen}/edit', [StudentCorrectionController::class, 'edit'])->name('students.edit');
    Route::post('/students/update-inline', [StudentCorrectionController::class, 'updateInline'])->name('students.update-inline');

    // Student Dropbox routes
    Route::get('/student-dropbox', [StudentDropboxController::class, 'index'])->name('students.dropbox');
    Route::get('/student-dropbox/filter', [StudentDropboxController::class, 'filter'])->name('students.dropbox.filter');
    Route::post('/student-dropbox/update', [StudentDropboxController::class, 'update'])->name('students.dropbox.update');

    Route::get('/students/status', [StudentStatusController::class, 'index'])->name('students.status');

    // Check student status
    Route::post('/students/check-status', [StudentStatusController::class, 'checkStatus'])->name('students.check-status');
    // Home route
//    Route::get('/home', function () {
//        return view('dashboard');
//    })->name('home');
});

Route::get('/getProfiles', [DashboardController::class, 'getProfiles'])->name('getProfiles');

