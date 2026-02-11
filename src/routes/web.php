<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceCorrectionController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendance;
use App\Http\Controllers\Admin\AuthController as AdminAuth;
use App\Http\Controllers\Admin\StaffController as AdminStaff;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;


Route::post('login', [AuthenticatedSessionController::class, 'store']);

Route::post('/register', [RegisteredUserController::class, 'store']);

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->name('verification.notice');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('resent', true);
})->middleware(['auth'])->name('verification.send');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/attendance');
})->middleware(['auth'])->name('verification.verify');



Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance');
    Route::post('/attendance/start', [AttendanceController::class, 'start']);
    Route::post('/attendance/break/start', [AttendanceController::class, 'breakStart'])->name('attendance.break.start');;
    Route::post('/attendance/break/end', [AttendanceController::class, 'breakEnd']);
    Route::post('/attendance/end', [AttendanceController::class, 'end']);
    Route::get('/attendance/list', [AttendanceController::class, 'list'])->name('attendance.list');
    Route::get('/attendance/detail/{date}', [AttendanceController::class, 'detail'])->name('attendance.detail');
    Route::get('/stamp_correction_request/list', [AttendanceCorrectionController::class, 'userList'])->name('correction-request.user.list');
    Route::post('/attendance/{attendance}/correction-request', [AttendanceCorrectionController::class, 'store'])->name('correction-request.store');
});


Route::get('/admin/login', [AdminAuth::class, 'adminLoginForm'])
    ->withoutMiddleware([RedirectIfAuthenticated::class])
    ->name('admin.login');
Route::post('/admin/login', [AdminAuth::class, 'login']);


Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/attendance/list', [AdminAttendance::class, 'list'])->name('attendance.list');
    Route::get('/attendance/detail/{id}', [AdminAttendance::class, 'detail'])->name('attendance.detail');
    Route::put('/attendance/detail/{attendance}', [AdminAttendance::class, 'update'])->name('attendance.update');
    Route::get('/staff/list', [AdminStaff::class, 'list'])->name('list');
    Route::get('attendance/staff/{id}', [AdminStaff::class, 'attendanceList'])->name('attendance_staff');
    Route::get('/attendance/staff/{id}/csv', [AdminAttendance::class, 'exportCsv'])->name('attendance.staff.csv');
    Route::get(
        '/stamp_correction_request/list',
        [AttendanceCorrectionController::class, 'adminList']
    )->name('correction-request.list');
    Route::get(
        '/stamp_correction_request/approve/{attendanceCorrection}',
        [AttendanceCorrectionController::class, 'approveShow']
    )->name('correction-request.approve.show');
    Route::post(
        '/stamp_correction_request/{attendanceCorrection}/approve',
        [AttendanceCorrectionController::class, 'approve']
    )->name('correction-request.approve');
});


Route::get('/', function () {
    return view('welcome');
});
