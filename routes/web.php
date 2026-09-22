<?php

use App\Features\Auth\Http\Controllers\AuthController;
use App\Features\Auth\Http\Controllers\DashboardController;
use App\Features\Website\Http\Controllers\WebsiteController;
use App\Http\Controllers\Admin\AboutController;
use App\Http\Controllers\Admin\AcademicCalendarController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AdmissionController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\CampusNewsController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\ClassRoutineController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\FeeController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\HeroSlideController;
use App\Http\Controllers\Admin\LibraryController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\NoticeController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PayrollController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\QrCodeController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ResultController;
use App\Http\Controllers\Admin\ScholarshipController;
use App\Http\Controllers\Admin\SchoolStatisticController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\TransportController;
use App\Http\Controllers\Admin\TrashController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Parent\ParentController;
use App\Http\Controllers\Website\ClassRoutineController as WebsiteClassRoutineController;
use App\Http\Controllers\Website\StudentController as WebsiteStudentController;
use App\Http\Controllers\Website\TeacherController as WebsiteTeacherController;
use Illuminate\Support\Facades\Route;

// ── Public Website ──
Route::middleware('track.visitor')->group(function () {
    Route::get('/', [WebsiteController::class, 'index'])->name('home');
    Route::get('/about', [WebsiteController::class, 'about'])->name('about');
    Route::get('/admission', [WebsiteController::class, 'admission'])->name('admission');
    Route::post('/admission', [WebsiteController::class, 'storeAdmission'])->name('admission.store');
    Route::get('/scholarship', [WebsiteController::class, 'scholarship'])->name('scholarship');
    Route::get('/scholarship/pdf/{registration_no}', [WebsiteController::class, 'scholarshipPdf'])->name('scholarship.pdf');
    Route::get('/contact', [WebsiteController::class, 'contact'])->name('contact');
    Route::post('/contact', [WebsiteController::class, 'sendContact'])->name('contact.send');
    Route::get('/notices', [WebsiteController::class, 'notices'])->name('notices');
    Route::get('/teachers', [WebsiteTeacherController::class, 'index'])->name('teachers');
    Route::get('/teacher/{slug}', [WebsiteTeacherController::class, 'single'])->name('teacher.single');
    Route::get('/testimonials', [WebsiteController::class, 'testimonials'])->name('testimonials');
    Route::post('/testimonials', [WebsiteController::class, 'storeTestimonial'])->middleware('throttle:5,1')->name('testimonials.submit');
    Route::get('/gallery', [WebsiteController::class, 'gallery'])->name('gallery');
    Route::get('/class-routine', [WebsiteClassRoutineController::class, 'index'])->name('class-routine');
    Route::get('/class-routine/grid', [WebsiteClassRoutineController::class, 'grid'])->name('class-routine.grid');
    Route::get('/student/id-card/{student}', [WebsiteStudentController::class, 'idCard'])->name('student.id-card');
    Route::get('/student/{student}/profile', [WebsiteStudentController::class, 'profile'])->name('student.profile');

    Route::prefix('academic')->name('academic.')->group(function () {
        Route::get('/calendar', [WebsiteController::class, 'academicCalendar'])->name('calendar');
        Route::get('/calendar/{academicCalendar}/pdf', [WebsiteController::class, 'academicCalendarPdf'])->name('calendar.pdf');
        Route::get('/fees', [WebsiteController::class, 'academicFees'])->name('fees');
        Route::get('/results', [WebsiteController::class, 'academicResults'])->name('results');
        Route::get('/facilities', [WebsiteController::class, 'academicFacilities'])->name('facilities');
    });
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/forgot-password', [AuthController::class, 'showLogin'])->name('password.request');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'role:admin,teacher', 'cache.headers:no_store'])->prefix('admin')->group(function () {
    Route::get('/', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');

    Route::get('/profile', [ProfileController::class, 'index'])->name('admin.profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('admin.profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('admin.profile.password');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('admin.notifications.index');
    Route::get('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('admin.notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('admin.notifications.read-all');

    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('admin.activity-logs.index');

    Route::middleware('role:admin')->prefix('users')->name('admin.users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::patch('/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('toggle-active');
    });

    Route::get('/about', [AboutController::class, 'index'])->name('admin.about.index');
    Route::post('/about/mission-vision', [AboutController::class, 'storeMissionVision'])->name('admin.about.mission-vision.store');
    Route::put('/about/mission-vision/{aboutContent}', [AboutController::class, 'updateMissionVision'])->name('admin.about.mission-vision.update');
    Route::delete('/about/mission-vision/{aboutContent}', [AboutController::class, 'destroyMissionVision'])->name('admin.about.mission-vision.destroy');
    Route::post('/about/core-values', [AboutController::class, 'storeCoreValue'])->name('admin.about.core-values.store');
    Route::get('/about/core-values/{coreValue}/edit', [AboutController::class, 'editCoreValue'])->name('admin.about.core-values.edit');
    Route::put('/about/core-values/{coreValue}', [AboutController::class, 'updateCoreValue'])->name('admin.about.core-values.update');
    Route::patch('/about/core-values/{coreValue}/toggle-active', [AboutController::class, 'toggleCoreValue'])->name('admin.about.core-values.toggle-active');
    Route::delete('/about/core-values/{coreValue}', [AboutController::class, 'destroyCoreValue'])->name('admin.about.core-values.destroy');
    Route::resource('testimonials', TestimonialController::class)->names('admin.testimonials');
    Route::patch('testimonials/{testimonial}/toggle-active', [TestimonialController::class, 'toggleActive'])->name('admin.testimonials.toggle-active');
    Route::resource('gallery', GalleryController::class)->names('admin.gallery');
    Route::patch('gallery/{galleryItem}/toggle-active', [GalleryController::class, 'toggleActive'])->name('admin.gallery.toggle-active');
    Route::resource('hero-slides', HeroSlideController::class)->names('admin.hero-slides');
    Route::patch('hero-slides/{heroSlide}/toggle-active', [HeroSlideController::class, 'toggleActive'])->name('admin.hero-slides.toggle-active');
    Route::get('/school-statistics', [SchoolStatisticController::class, 'index'])->middleware('role:admin')->name('admin.school-statistics.index');
    Route::post('/school-statistics', [SchoolStatisticController::class, 'store'])->middleware('role:admin')->name('admin.school-statistics.store');
    Route::put('/school-statistics/{id}', [SchoolStatisticController::class, 'update'])->middleware('role:admin')->name('admin.school-statistics.update');
    Route::resource('academic-calendars', AcademicCalendarController::class)->names('admin.academic-calendars');
    Route::patch('academic-calendars/{academicCalendar}/toggle-active', [AcademicCalendarController::class, 'toggleActive'])->name('admin.academic-calendars.toggle-active');
    Route::resource('messages', MessageController::class)->names('admin.messages');
    Route::patch('messages/{message}/toggle-active', [MessageController::class, 'toggleActive'])->name('admin.messages.toggle-active');
    Route::resource('faqs', FaqController::class)->names('admin.faqs');
    Route::patch('faqs/{faq}/toggle-active', [FaqController::class, 'toggleActive'])->name('admin.faqs.toggle-active');

    Route::get('/trash', [TrashController::class, 'index'])->name('admin.trash.index');
    Route::post('/trash/{type}/{id}/restore', [TrashController::class, 'restore'])->name('admin.trash.restore');
    Route::delete('/trash/{type}/{id}/force-delete', [TrashController::class, 'forceDelete'])->name('admin.trash.force-delete');

    Route::get('students/download', [StudentController::class, 'downloadAll'])->name('admin.students.download');
    Route::resource('students', StudentController::class)->except(['create'])->names('admin.students');
    Route::patch('students/{student}/toggle-active', [StudentController::class, 'toggleActive'])->name('admin.students.toggle-active');
    Route::resource('classes', ClassController::class)->except(['create'])->names('admin.classes');
    Route::resource('subjects', SubjectController::class)->except(['create'])->names('admin.subjects');

    Route::get('/teachers', [TeacherController::class, 'index'])->name('admin.teachers.index');
    Route::post('/teachers', [TeacherController::class, 'store'])->name('admin.teachers.store');
    Route::get('/teachers/download', [TeacherController::class, 'download'])->name('admin.teachers.download');
    Route::get('/teachers/{teacher}/edit', [TeacherController::class, 'edit'])->name('admin.teachers.edit');
    Route::get('/teachers/{teacher}', [TeacherController::class, 'show'])->name('admin.teachers.show');
    Route::put('/teachers/{teacher}', [TeacherController::class, 'update'])->name('admin.teachers.update');
    Route::delete('/teachers/{teacher}', [TeacherController::class, 'destroy'])->name('admin.teachers.destroy');
    Route::patch('/teachers/{teacher}/toggle-active', [TeacherController::class, 'toggleActive'])->name('admin.teachers.toggle-active');

    Route::get('notices/download', [NoticeController::class, 'downloadAll'])->name('admin.notices.download');
    Route::patch('notices/{notice}/toggle-active', [NoticeController::class, 'toggleActive'])->name('admin.notices.toggle-active');
    Route::resource('notices', NoticeController::class)->names('admin.notices');

    Route::get('/campus-news', [CampusNewsController::class, 'index'])->name('admin.campus-news.index');
    Route::post('/campus-news', [CampusNewsController::class, 'store'])->name('admin.campus-news.store');
    Route::get('/campus-news/download', [CampusNewsController::class, 'downloadAll'])->name('admin.campus-news.download');
    Route::get('/campus-news/{campusNews}/edit', [CampusNewsController::class, 'edit'])->name('admin.campus-news.edit');
    Route::get('/campus-news/{campusNews}/show', [CampusNewsController::class, 'show'])->name('admin.campus-news.show');
    Route::put('/campus-news/{campusNews}', [CampusNewsController::class, 'update'])->name('admin.campus-news.update');
    Route::patch('/campus-news/{campusNews}/toggle-active', [CampusNewsController::class, 'toggleActive'])->name('admin.campus-news.toggle-active');
    Route::delete('/campus-news/{campusNews}', [CampusNewsController::class, 'destroy'])->name('admin.campus-news.destroy');

    Route::get('/scholarship', [ScholarshipController::class, 'index'])->name('admin.scholarship.index');
    Route::post('/scholarship', [ScholarshipController::class, 'store'])->name('admin.scholarship.store');
    Route::get('/scholarship/{scholarshipRegistration}/show', [ScholarshipController::class, 'show'])->name('admin.scholarship.show');
    Route::get('/scholarship/{scholarshipRegistration}/edit', [ScholarshipController::class, 'edit'])->name('admin.scholarship.edit');
    Route::put('/scholarship/{scholarshipRegistration}', [ScholarshipController::class, 'update'])->name('admin.scholarship.update');
    Route::patch('/scholarship/{scholarshipRegistration}/status', [ScholarshipController::class, 'updateStatus'])->name('admin.scholarship.status');
    Route::delete('/scholarship/{scholarshipRegistration}', [ScholarshipController::class, 'destroy'])->name('admin.scholarship.destroy');
    Route::get('/scholarship/{scholarshipRegistration}/pdf', [ScholarshipController::class, 'print'])->name('admin.scholarship.pdf');
    Route::get('/scholarship/download', [ScholarshipController::class, 'downloadAll'])->name('admin.scholarship.download');

    Route::get('/admission', [AdmissionController::class, 'index'])->name('admin.admission.index');
    Route::post('/admission', [AdmissionController::class, 'store'])->name('admin.admission.store');
    Route::get('/admission/{admission}/show', [AdmissionController::class, 'show'])->name('admin.admission.show');
    Route::get('/admission/{admission}/edit', [AdmissionController::class, 'edit'])->name('admin.admission.edit');
    Route::put('/admission/{admission}', [AdmissionController::class, 'update'])->name('admin.admission.update');
    Route::patch('/admission/{admission}/status', [AdmissionController::class, 'updateStatus'])->name('admin.admission.status');
    Route::post('/admission/{admission}/admit', [AdmissionController::class, 'admit'])->name('admin.admission.admit');
    Route::delete('/admission/{admission}', [AdmissionController::class, 'destroy'])->name('admin.admission.destroy');
    Route::get('/admission/{admission}/pdf', [AdmissionController::class, 'print'])->name('admin.admission.pdf');
    Route::get('/admission/download', [AdmissionController::class, 'downloadAll'])->name('admin.admission.download');

    Route::get('/class-routines', [ClassRoutineController::class, 'index'])->name('admin.class-routines.index');
    Route::get('/class-routines/download', [ClassRoutineController::class, 'download'])->name('admin.class-routines.download');
    Route::post('/class-routines', [ClassRoutineController::class, 'store'])->name('admin.class-routines.store');
    Route::get('/class-routines/{classRoutine}/show', [ClassRoutineController::class, 'show'])->name('admin.class-routines.show');
    Route::get('/class-routines/{classRoutine}/edit', [ClassRoutineController::class, 'edit'])->name('admin.class-routines.edit');
    Route::put('/class-routines/{classRoutine}', [ClassRoutineController::class, 'update'])->name('admin.class-routines.update');
    Route::delete('/class-routines/{classRoutine}', [ClassRoutineController::class, 'destroy'])->name('admin.class-routines.destroy');

    Route::get('/library/borrowings', [LibraryController::class, 'borrowings'])->name('admin.library.borrowings');
    Route::post('/library/borrow', [LibraryController::class, 'borrow'])->name('admin.library.borrow');
    Route::post('/library/return/{borrowing}', [LibraryController::class, 'returnBook'])->name('admin.library.return');
    Route::resource('library', LibraryController::class)->except(['create'])->names('admin.library');

    Route::get('/transport/{bus}/routes', [TransportController::class, 'routes'])->name('admin.transport.routes');
    Route::post('/transport/{bus}/routes', [TransportController::class, 'storeRoute'])->name('admin.transport.routes.store');
    Route::post('/transport/assign', [TransportController::class, 'assignStudent'])->name('admin.transport.assign');
    Route::resource('transport', TransportController::class)->except(['create'])->names('admin.transport');
    Route::resource('staff', StaffController::class)->except(['create'])->names('admin.staff');
    Route::patch('staff/{staff}/toggle-active', [StaffController::class, 'toggleActive'])->name('admin.staff.toggle-active');

    Route::get('/contact-messages', [ContactMessageController::class, 'index'])->name('admin.contact-messages.index');
    Route::get('/contact-messages/{contactMessage}', [ContactMessageController::class, 'show'])->name('admin.contact-messages.show');
    Route::patch('/contact-messages/{contactMessage}/read', [ContactMessageController::class, 'markRead'])->name('admin.contact-messages.read');
    Route::delete('/contact-messages/{contactMessage}', [ContactMessageController::class, 'destroy'])->name('admin.contact-messages.destroy');

    Route::get('/qrcode', [QrCodeController::class, 'index'])->name('admin.qrcode');
    Route::get('/qrcode/generate', [QrCodeController::class, 'generate'])->name('admin.qrcode.generate');

    Route::resource('attendance', AttendanceController::class)->except(['create'])->names('admin.attendance');

    Route::get('/exams/{exam}/results', [ExamController::class, 'results'])->name('admin.exams.results');
    Route::post('/exams/{exam}/results', [ExamController::class, 'storeResults'])->name('admin.exams.results.store');
    Route::resource('exams', ExamController::class)->except(['create'])->names('admin.exams');

    Route::get('/results', [ResultController::class, 'index'])->name('admin.results.index');
    Route::get('/results/export', [ResultController::class, 'export'])->name('admin.results.export');

    Route::get('/fees/structures', [FeeController::class, 'structures'])->name('admin.fees.structures');
    Route::post('/fees/structures', [FeeController::class, 'storeStructure'])->name('admin.fees.structures.store');
    Route::get('/fees/structures/{feeStructure}/edit', [FeeController::class, 'editStructure'])->name('admin.fees.structures.edit');
    Route::put('/fees/structures/{feeStructure}', [FeeController::class, 'updateStructure'])->name('admin.fees.structures.update');
    Route::delete('/fees/structures/{feeStructure}', [FeeController::class, 'destroyStructure'])->name('admin.fees.structures.destroy');
    Route::get('/fees/invoices', [FeeController::class, 'invoices'])->name('admin.fees.invoices');
    Route::post('/fees/invoices/generate', [FeeController::class, 'generateInvoices'])->name('admin.fees.invoices.generate');
    Route::get('/fees/payments', [FeeController::class, 'payments'])->name('admin.fees.payments');
    Route::post('/fees/payments', [FeeController::class, 'recordPayment'])->name('admin.fees.payments.record');
    Route::resource('fees', FeeController::class)->except(['create', 'store', 'show', 'update'])->names('admin.fees');

    Route::post('/payroll/process', [PayrollController::class, 'process'])->name('admin.payroll.process');
    Route::patch('/payroll/{payroll}/toggle-status', [PayrollController::class, 'toggleStatus'])->name('admin.payroll.toggle-status');
    Route::resource('payroll', PayrollController::class)->except(['edit', 'destroy'])->names('admin.payroll');

    Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/reports/students', [ReportController::class, 'studentReport'])->name('admin.reports.students');
    Route::get('/reports/classes', [ReportController::class, 'classReport'])->name('admin.reports.class');
    Route::get('/reports/attendance', [ReportController::class, 'attendanceReport'])->name('admin.reports.attendance');
    Route::get('/reports/exams', [ReportController::class, 'examReport'])->name('admin.reports.exams');
    Route::get('/reports/fees', [ReportController::class, 'feeReport'])->name('admin.reports.fees');
    Route::get('/reports/staff', [ReportController::class, 'staffReport'])->name('admin.reports.staff');
    Route::get('/reports/transport', [ReportController::class, 'transportReport'])->name('admin.reports.transport');
});

Route::middleware(['auth', 'role:parent', 'cache.headers:no_store'])->prefix('parent')->name('parent.')->group(function () {
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
