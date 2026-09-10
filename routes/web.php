<?php

use App\Features\Auth\Http\Controllers\AuthController;
use App\Features\Auth\Http\Controllers\DashboardController;
use App\Features\Website\Http\Controllers\WebsiteController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\FeeController;
use App\Http\Controllers\Admin\LibraryController;
use App\Http\Controllers\Admin\NoticeController;
use App\Http\Controllers\Admin\PayrollController;
use App\Http\Controllers\Admin\QrCodeController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ScholarshipController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\TransportController;
use App\Http\Controllers\Parent\ParentController;
use Illuminate\Support\Facades\Route;

// ── Public Website ──
Route::get('/', [WebsiteController::class, 'index'])->name('home');
Route::get('/about', [WebsiteController::class, 'about'])->name('about');
Route::get('/admission', [WebsiteController::class, 'admission'])->name('admission');
Route::get('/scholarship', [WebsiteController::class, 'scholarship'])->name('scholarship');
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

    Route::resource('students', StudentController::class)->names('admin.students');
    Route::resource('classes', ClassController::class)->names('admin.classes');
    Route::resource('subjects', SubjectController::class)->names('admin.subjects');
    Route::resource('teachers', TeacherController::class)->names('admin.teachers');
    Route::resource('notices', NoticeController::class)->names('admin.notices');

    // Scholarship (custom routes before anything else)
    Route::get('/scholarship', [ScholarshipController::class, 'index'])->name('admin.scholarship.index');
    Route::get('/scholarship/create', [ScholarshipController::class, 'create'])->name('admin.scholarship.create');
    Route::get('/scholarship/{scholarshipRegistration}', [ScholarshipController::class, 'show'])->name('admin.scholarship.show');
    Route::delete('/scholarship/{scholarshipRegistration}', [ScholarshipController::class, 'destroy'])->name('admin.scholarship.destroy');
    Route::get('/scholarship/{scholarshipRegistration}/pdf', [ScholarshipController::class, 'print'])->name('admin.scholarship.pdf');

    // Library (custom routes before resource)
    Route::get('/library/borrowings', [LibraryController::class, 'borrowings'])->name('admin.library.borrowings');
    Route::post('/library/borrow', [LibraryController::class, 'borrow'])->name('admin.library.borrow');
    Route::post('/library/return/{borrowing}', [LibraryController::class, 'returnBook'])->name('admin.library.return');
    Route::resource('library', LibraryController::class)->names('admin.library');

    // Transport (custom routes before resource)
    Route::get('/transport/{bus}/routes', [TransportController::class, 'routes'])->name('admin.transport.routes');
    Route::post('/transport/{bus}/routes', [TransportController::class, 'storeRoute'])->name('admin.transport.routes.store');
    Route::post('/transport/assign', [TransportController::class, 'assignStudent'])->name('admin.transport.assign');
    Route::resource('transport', TransportController::class)->names('admin.transport');
    Route::resource('staff', StaffController::class)->names('admin.staff');

    // QR Code Generator
    Route::get('/qrcode', [QrCodeController::class, 'index'])->name('admin.qrcode');
    Route::get('/qrcode/generate', [QrCodeController::class, 'generate'])->name('admin.qrcode.generate');

    // Attendance (custom routes before resource)
    Route::get('/attendance/select', [AttendanceController::class, 'selectClass'])->name('admin.attendance.select');
    Route::resource('attendance', AttendanceController::class)->names('admin.attendance');

    // Exams (custom routes before resource)
    Route::get('/exams/{exam}/results', [ExamController::class, 'results'])->name('admin.exams.results');
    Route::post('/exams/{exam}/results', [ExamController::class, 'storeResults'])->name('admin.exams.results.store');
    Route::resource('exams', ExamController::class)->names('admin.exams');

    // Fees (custom routes before resource)
    Route::get('/fees/structures', [FeeController::class, 'structures'])->name('admin.fees.structures');
    Route::get('/fees/structures/create', [FeeController::class, 'createStructure'])->name('admin.fees.structures.create');
    Route::post('/fees/structures', [FeeController::class, 'storeStructure'])->name('admin.fees.structures.store');
    Route::get('/fees/invoices', [FeeController::class, 'invoices'])->name('admin.fees.invoices');
    Route::post('/fees/invoices/generate', [FeeController::class, 'generateInvoices'])->name('admin.fees.invoices.generate');
    Route::get('/fees/payments', [FeeController::class, 'payments'])->name('admin.fees.payments');
    Route::post('/fees/payments', [FeeController::class, 'recordPayment'])->name('admin.fees.payments.record');
    Route::resource('fees', FeeController::class)->except(['create', 'store'])->names('admin.fees');

    // Payroll (custom routes before resource)
    Route::post('/payroll/process', [PayrollController::class, 'process'])->name('admin.payroll.process');
    Route::resource('payroll', PayrollController::class)->names('admin.payroll');

    // Reports (custom routes)
    Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/reports/students', [ReportController::class, 'studentReport'])->name('admin.reports.students');
    Route::get('/reports/classes', [ReportController::class, 'classReport'])->name('admin.reports.class');
    Route::get('/reports/attendance', [ReportController::class, 'attendanceReport'])->name('admin.reports.attendance');
    Route::get('/reports/exams', [ReportController::class, 'examReport'])->name('admin.reports.exams');
    Route::get('/reports/fees', [ReportController::class, 'feeReport'])->name('admin.reports.fees');
    Route::get('/reports/staff', [ReportController::class, 'staffReport'])->name('admin.reports.staff');
    Route::get('/reports/transport', [ReportController::class, 'transportReport'])->name('admin.reports.transport');
});

// ── Parent Portal ──
Route::middleware(['auth', 'role:parent'])->prefix('parent')->name('parent.')->group(function () {
    Route::get('/', [DashboardController::class, 'parentDashboard'])->name('dashboard');
    Route::get('/children', [ParentController::class, 'index'])->name('children');
    Route::get('/child/{student}', [ParentController::class, 'childDetail'])->name('children.show');
    Route::get('/attendance', [ParentController::class, 'attendance'])->name('attendance');
    Route::get('/attendance/{student}', [ParentController::class, 'attendanceForStudent'])->name('attendance.student');
    Route::get('/fees', [ParentController::class, 'fees'])->name('fees');
    Route::get('/fees/history', [ParentController::class, 'feeHistory'])->name('fees.history');
    Route::get('/fees/invoice/{invoice}', [ParentController::class, 'feeInvoice'])->name('fees.show');
    Route::get('/fees/{student}', [ParentController::class, 'feesForStudent'])->name('fees.student');
    Route::get('/notices', [ParentController::class, 'notices'])->name('notices');
    Route::get('/exams', [ParentController::class, 'exams'])->name('exams');
});
