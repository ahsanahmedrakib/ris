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
    Route::post('/login', [AuthController::class, 'apiLogin']);
    Route::post('/logout', [AuthController::class, 'apiLogout'])->middleware('auth:api');
    Route::post('/refresh', [AuthController::class, 'apiRefresh'])->middleware('auth:api');
    Route::get('/me', [AuthController::class, 'apiMe'])->middleware('auth:api');
});

Route::middleware('auth:api')->group(function () {
    Route::resource('students', StudentController::class);
    Route::resource('classes', ClassController::class);
    Route::resource('subjects', SubjectController::class);
    Route::resource('attendance', AttendanceController::class);
    Route::resource('exams', ExamController::class);
    Route::resource('fees', FeeController::class);
    Route::resource('notices', NoticeController::class);
    Route::resource('books', BookController::class);
    Route::resource('transport', TransportController::class);
    Route::resource('staff', StaffController::class);
    Route::resource('payroll', PayrollController::class);
});
