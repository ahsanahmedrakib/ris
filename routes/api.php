<?php

use App\Features\Auth\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'apiLogin']);
    Route::post('/logout', [AuthController::class, 'apiLogout'])->middleware('auth:api');
    Route::post('/refresh', [AuthController::class, 'apiRefresh'])->middleware('auth:api');
    Route::get('/me', [AuthController::class, 'apiMe'])->middleware('auth:api');
});

Route::middleware('auth:api')->group(function () {
    Route::resource('students', \App\Http\Controllers\Api\StudentController::class);
    Route::resource('classes', \App\Http\Controllers\Api\ClassController::class);
    Route::resource('subjects', \App\Http\Controllers\Api\SubjectController::class);
    Route::resource('attendance', \App\Http\Controllers\Api\AttendanceController::class);
    Route::resource('exams', \App\Http\Controllers\Api\ExamController::class);
    Route::resource('fees', \App\Http\Controllers\Api\FeeController::class);
    Route::resource('notices', \App\Http\Controllers\Api\NoticeController::class);
    Route::resource('books', \App\Http\Controllers\Api\BookController::class);
    Route::resource('transport', \App\Http\Controllers\Api\TransportController::class);
    Route::resource('staff', \App\Http\Controllers\Api\StaffController::class);
    Route::resource('payroll', \App\Http\Controllers\Api\PayrollController::class);
});
