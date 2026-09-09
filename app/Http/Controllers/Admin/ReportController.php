<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AttendanceStatus;
use App\Enums\FeeStatus;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\FeeInvoice;
use App\Models\FeePayment;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Bus;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_students' => Student::where('is_active', true)->count(),
            'total_classes' => ClassRoom::count(),
            'total_exams' => Exam::count(),
            'fees_collected' => FeePayment::sum('amount'),
        ];

        return view('admin.reports.index', compact('stats'));
    }

    public function studentReport(Request $request): View
    {
        $students = Student::with(['user', 'classRoom'])->where('is_active', true)->orderBy('roll_no')->paginate(20);
        $classes = ClassRoom::orderBy('name')->get();

        $selectedStudent = null;
        if ($request->filled('student_id')) {
            $selectedStudent = Student::with([
                'user',
                'classRoom',
                'examResults' => fn ($q) => $q->with(['exam', 'subject']),
                'attendances' => fn ($q) => $q->latest('date'),
            ])->find($request->student_id);
        }

        return view('admin.reports.student', compact('students', 'classes', 'selectedStudent'));
    }

    public function classReport(Request $request): View
    {
        $classes = ClassRoom::withCount('students')->orderBy('name')->get();
        $class = null;
        $examResults = collect();

        if ($request->filled('class_id')) {
            $class = ClassRoom::with([
                'students' => fn ($q) => $q->with('user')->orderBy('roll_no'),
                'academicYear',
            ])->withCount('students')->find($request->class_id);

            $examResults = ExamResult::whereHas('student', fn ($q) => $q->where('class_id', $request->class_id))
                ->with(['student.user', 'subject', 'exam'])
                ->get()
                ->groupBy('student_id');
        }

        return view('admin.reports.class', compact('classes', 'class', 'examResults'));
    }

    public function attendanceReport(Request $request): View
    {
        $query = Attendance::with(['student.user', 'classRoom']);

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->latest('date')->paginate(20)->withQueryString();
        $classes = ClassRoom::orderBy('name')->get();
        $statuses = AttendanceStatus::cases();

        $summary = [
            'total' => $attendances->total(),
            'present' => (clone $query)->where('status', AttendanceStatus::Present->value)->count(),
            'absent' => (clone $query)->where('status', AttendanceStatus::Absent->value)->count(),
            'late' => (clone $query)->where('status', AttendanceStatus::Late->value)->count(),
            'excused' => (clone $query)->where('status', AttendanceStatus::Excused->value)->count(),
        ];

        return view('admin.reports.attendance', compact('attendances', 'classes', 'statuses', 'summary'));
    }

    public function examReport(Request $request): View
    {
        $exams = Exam::with(['classRoom', 'academicYear'])->orderByDesc('created_at')->paginate(20);
        $classes = ClassRoom::orderBy('name')->get();

        return view('admin.reports.exam', compact('exams', 'classes'));
    }

    public function feeReport(Request $request): View
    {
        $query = FeePayment::with(['student.user', 'invoice.feeStructure']);

        if ($request->filled('start_date')) {
            $query->whereDate('paid_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('paid_at', '<=', $request->end_date);
        }

        $payments = $query->latest('paid_at')->paginate(20)->withQueryString();

        $summary = [
            'total_collected' => FeePayment::sum('amount'),
            'total_pending' => FeeInvoice::whereIn('status', [FeeStatus::Pending, FeeStatus::Partial])->sum('amount'),
            'total_overdue' => FeeInvoice::where('status', FeeStatus::Overdue)->sum('amount'),
            'total_invoices' => FeeInvoice::count(),
            'paid_invoices' => FeeInvoice::where('status', FeeStatus::Paid)->count(),
        ];

        return view('admin.reports.fee', compact('payments', 'summary'));
    }

    public function staffReport(): View
    {
        $staff = Staff::with('user')->orderBy('employee_id')->paginate(20);
        return view('admin.reports.staff', compact('staff'));
    }

    public function transportReport(): View
    {
        $buses = Bus::withCount('studentTransport')->orderBy('bus_no')->paginate(20);
        return view('admin.reports.transport', compact('buses'));
    }
}
