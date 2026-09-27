<?php

use App\Features\Auth\Http\Controllers\AuthController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\ClassController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\FeeController;
use App\Http\Controllers\Api\NoticeController;
use App\Http\Controllers\Api\PayrollController;
use App\Http\Controllers\Api\StaffController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\TransportController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'apiLogin'])->middleware('throttle:api-login')->name('api.auth.login');
    Route::post('/logout', [AuthController::class, 'apiLogout'])->middleware(['auth:api', 'throttle:api'])->name('api.auth.logout');
    Route::post('/refresh', [AuthController::class, 'apiRefresh'])->middleware(['auth:api', 'throttle:api'])->name('api.auth.refresh');
    Route::get('/me', [AuthController::class, 'apiMe'])->middleware('auth:api')->name('api.auth.me');
});

// Academic records are shared by staff; anything touching money or staff
// administration stays with accounts admins.
Route::middleware(['auth:api', 'throttle:api', 'role:admin,teacher'])->group(function () {
    Route::resource('students', StudentController::class)->names('api.students');
    Route::resource('classes', ClassController::class)->names('api.classes');
    Route::resource('subjects', SubjectController::class)->names('api.subjects');
    Route::resource('attendance', AttendanceController::class)->names('api.attendance');
    Route::resource('exams', ExamController::class)->names('api.exams');
    Route::resource('notices', NoticeController::class)->names('api.notices');
    Route::resource('books', BookController::class)->names('api.books');
});

Route::middleware(['auth:api', 'throttle:api', 'role:admin'])->group(function () {
    Route::resource('fees', FeeController::class)->names('api.fees');
    Route::resource('transport', TransportController::class)->names('api.transport');
    Route::resource('staff', StaffController::class)->names('api.staff');
    Route::resource('payroll', PayrollController::class)->names('api.payroll');
});
