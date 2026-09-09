<?php

use App\Features\Auth\Http\Controllers\AuthController;
use App\Features\Auth\Http\Controllers\DashboardController;
use App\Features\Website\Http\Controllers\WebsiteController;
use Illuminate\Support\Facades\Route;

// ── Public Website ──
Route::get('/', [WebsiteController::class, 'index'])->name('home');
Route::get('/about', [WebsiteController::class, 'about'])->name('about');
Route::get('/admission', [WebsiteController::class, 'admission'])->name('admission');
Route::get('/contact', [WebsiteController::class, 'contact'])->name('contact');
Route::post('/contact', [WebsiteController::class, 'sendContact'])->name('contact.send');
Route::get('/notices', [WebsiteController::class, 'notices'])->name('notices');

// ── Auth ──
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/forgot-password', [AuthController::class, 'showLogin'])->name('password.request');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ── Admin Panel ──
Route::middleware(['auth', 'role:admin,teacher'])->prefix('admin')->group(function () {
    Route::get('/', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');

    Route::resource('students', \App\Http\Controllers\Admin\StudentController::class)->names('admin.students');
    Route::resource('classes', \App\Http\Controllers\Admin\ClassController::class)->names('admin.classes');
    Route::resource('subjects', \App\Http\Controllers\Admin\SubjectController::class)->names('admin.subjects');
    Route::resource('teachers', \App\Http\Controllers\Admin\TeacherController::class)->names('admin.teachers');
    Route::resource('notices', \App\Http\Controllers\Admin\NoticeController::class)->names('admin.notices');
    Route::resource('library', \App\Http\Controllers\Admin\LibraryController::class)->names('admin.library');
    Route::resource('transport', \App\Http\Controllers\Admin\TransportController::class)->names('admin.transport');
    Route::resource('staff', \App\Http\Controllers\Admin\StaffController::class)->names('admin.staff');

    // QR Code Generator
    Route::get('/qrcode', [\App\Http\Controllers\Admin\QrCodeController::class, 'index'])->name('admin.qrcode');
    Route::get('/qrcode/generate', [\App\Http\Controllers\Admin\QrCodeController::class, 'generate'])->name('admin.qrcode.generate');

    // Attendance (custom routes before resource)
    Route::get('/attendance/select', [\App\Http\Controllers\Admin\AttendanceController::class, 'selectClass'])->name('admin.attendance.select');
    Route::resource('attendance', \App\Http\Controllers\Admin\AttendanceController::class)->names('admin.attendance');

    // Exams (custom routes before resource)
    Route::get('/exams/{exam}/results', [\App\Http\Controllers\Admin\ExamController::class, 'results'])->name('admin.exams.results');
    Route::post('/exams/{exam}/results', [\App\Http\Controllers\Admin\ExamController::class, 'storeResults'])->name('admin.exams.results.store');
    Route::resource('exams', \App\Http\Controllers\Admin\ExamController::class)->names('admin.exams');

    // Fees (custom routes before resource)
    Route::get('/fees/structures', [\App\Http\Controllers\Admin\FeeController::class, 'structures'])->name('admin.fees.structures');
    Route::get('/fees/structures/create', [\App\Http\Controllers\Admin\FeeController::class, 'createStructure'])->name('admin.fees.structures.create');
    Route::post('/fees/structures', [\App\Http\Controllers\Admin\FeeController::class, 'storeStructure'])->name('admin.fees.structures.store');
    Route::get('/fees/invoices', [\App\Http\Controllers\Admin\FeeController::class, 'invoices'])->name('admin.fees.invoices');
    Route::post('/fees/invoices/generate', [\App\Http\Controllers\Admin\FeeController::class, 'generateInvoices'])->name('admin.fees.invoices.generate');
    Route::get('/fees/payments', [\App\Http\Controllers\Admin\FeeController::class, 'payments'])->name('admin.fees.payments');
    Route::post('/fees/payments', [\App\Http\Controllers\Admin\FeeController::class, 'recordPayment'])->name('admin.fees.payments.record');
    Route::resource('fees', \App\Http\Controllers\Admin\FeeController::class)->except(['create', 'store'])->names('admin.fees');

    // Payroll (custom routes before resource)
    Route::post('/payroll/process', [\App\Http\Controllers\Admin\PayrollController::class, 'process'])->name('admin.payroll.process');
    Route::resource('payroll', \App\Http\Controllers\Admin\PayrollController::class)->names('admin.payroll');

    // Reports (custom routes)
    Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/reports/students', [\App\Http\Controllers\Admin\ReportController::class, 'studentReport'])->name('admin.reports.students');
    Route::get('/reports/classes', [\App\Http\Controllers\Admin\ReportController::class, 'classReport'])->name('admin.reports.class');
    Route::get('/reports/attendance', [\App\Http\Controllers\Admin\ReportController::class, 'attendanceReport'])->name('admin.reports.attendance');
    Route::get('/reports/exams', [\App\Http\Controllers\Admin\ReportController::class, 'examReport'])->name('admin.reports.exams');
    Route::get('/reports/fees', [\App\Http\Controllers\Admin\ReportController::class, 'feeReport'])->name('admin.reports.fees');
    Route::get('/reports/staff', [\App\Http\Controllers\Admin\ReportController::class, 'staffReport'])->name('admin.reports.staff');
    Route::get('/reports/transport', [\App\Http\Controllers\Admin\ReportController::class, 'transportReport'])->name('admin.reports.transport');
});

// ── Parent Portal ──
Route::middleware(['auth', 'role:parent'])->prefix('parent')->name('parent.')->group(function () {
    Route::get('/', [DashboardController::class, 'parentDashboard'])->name('dashboard');
    Route::get('/children', [\App\Http\Controllers\Parent\ParentController::class, 'index'])->name('children');
    Route::get('/child/{student}', [\App\Http\Controllers\Parent\ParentController::class, 'childDetail'])->name('children.show');
    Route::get('/attendance', [\App\Http\Controllers\Parent\ParentController::class, 'attendance'])->name('attendance');
    Route::get('/attendance/{student}', [\App\Http\Controllers\Parent\ParentController::class, 'attendanceForStudent'])->name('attendance.student');
    Route::get('/fees', [\App\Http\Controllers\Parent\ParentController::class, 'fees'])->name('fees');
    Route::get('/fees/history', [\App\Http\Controllers\Parent\ParentController::class, 'feeHistory'])->name('fees.history');
    Route::get('/fees/invoice/{invoice}', [\App\Http\Controllers\Parent\ParentController::class, 'feeInvoice'])->name('fees.show');
    Route::get('/fees/{student}', [\App\Http\Controllers\Parent\ParentController::class, 'feesForStudent'])->name('fees.student');
    Route::get('/notices', [\App\Http\Controllers\Parent\ParentController::class, 'notices'])->name('notices');
    Route::get('/exams', [\App\Http\Controllers\Parent\ParentController::class, 'exams'])->name('exams');
});
