<?php

namespace App\Http\Controllers\Parent;

use App\Enums\AttendanceStatus;
use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\FeeInvoice;
use App\Models\FeePayment;
use App\Models\Notice;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParentController extends Controller
{
    public function index(): View
    {
        $parent = auth()->user();
        $children = $parent->parentStudents()->with(['classRoom', 'user'])->get();

        return view('parent.children.index', compact('children'));
    }

    public function childDetail($id): View
    {
        $parent = auth()->user();

        $student = Student::with([
            'user',
            'classRoom',
            'attendances' => fn ($q) => $q->latest('date')->take(30),
            'examResults' => fn ($q) => $q->with(['exam', 'subject'])->latest(),
            'feeInvoices' => fn ($q) => $q->with('feeStructure')->latest(),
            'bus',
        ])->findOrFail($id);

        if (! $parent->parentStudents()->where('student_id', $id)->exists()) {
            abort(403, 'আপনি এই ছাত্র/ছাত্রীর তথ্য দেখতে পারবেন না।');
        }

        // Attendance summary
        $totalAttendance = $student->attendances()->count();
        $presentCount = $student->attendances()->where('status', AttendanceStatus::Present->value)->count();

        return view('parent.children.show', compact('student', 'totalAttendance', 'presentCount'));
    }

    public function attendance(Request $request): View
    {
        $parent = auth()->user();
        $children = $parent->parentStudents()->with('classRoom')->get();

        $selectedChildId = $request->get('student_id');
        $attendances = collect();
        $totalPresent = 0;
        $totalAbsent = 0;
        $totalDays = 0;

        if ($selectedChildId && $children->firstWhere('id', $selectedChildId)) {
            $student = $children->firstWhere('id', $selectedChildId);

            $attendances = $student->attendances()
                ->with('classRoom')
                ->latest('date')
                ->paginate(20)
                ->withQueryString();

            $totalDays = $student->attendances()->count();
            $totalPresent = $student->attendances()->where('status', AttendanceStatus::Present->value)->count();
            $totalAbsent = $student->attendances()->where('status', AttendanceStatus::Absent->value)->count();
        }

        return view('parent.attendance.index', compact('children', 'attendances', 'selectedChildId', 'totalPresent', 'totalAbsent', 'totalDays'));
    }

    public function attendanceForStudent(Request $request, $studentId): View
    {
        $request->merge(['student_id' => $studentId]);
        return $this->attendance($request);
    }

    public function fees(Request $request): View
    {
        $parent = auth()->user();
        $children = $parent->parentStudents()->with('classRoom')->get();

        $selectedChildId = $request->get('student_id');
        $invoices = collect();
        $totalPending = 0;
        $totalPaid = 0;

        if ($selectedChildId && $children->firstWhere('id', $selectedChildId)) {
            $invoices = FeeInvoice::with(['feeStructure', 'feePayments'])
                ->where('student_id', $selectedChildId)
                ->latest()
                ->paginate(15)
                ->withQueryString();

            $totalPending = FeeInvoice::where('student_id', $selectedChildId)
                ->whereIn('status', ['pending', 'partial'])
                ->sum('amount');

            $totalPaid = FeePayment::where('student_id', $selectedChildId)->sum('amount');
        }

        return view('parent.fees.index', compact('children', 'invoices', 'selectedChildId', 'totalPending', 'totalPaid'));
    }

    public function feesForStudent(Request $request, $studentId): View
    {
        $request->merge(['student_id' => $studentId]);
        return $this->fees($request);
    }

    public function feeHistory(): View
    {
        $parent = auth()->user();
        $children = $parent->parentStudents()->pluck('id');

        $payments = FeePayment::with(['student', 'invoice.feeStructure'])
            ->whereIn('student_id', $children)
            ->latest('paid_at')
            ->paginate(20);

        return view('parent.fees.history', compact('payments'));
    }

    public function feeInvoice($id): View
    {
        $parent = auth()->user();
        $children = $parent->parentStudents()->pluck('id');

        $invoice = FeeInvoice::with(['student', 'student.classRoom', 'feeStructure', 'feePayments'])
            ->findOrFail($id);

        if (! $children->contains($invoice->student_id)) {
            abort(403, 'আপনি এই ইনভয়েস দেখতে পারবেন না।');
        }

        return view('parent.fees.show', compact('invoice'));
    }

    public function notices(): View
    {
        $notices = Notice::where('is_active', true)
            ->where('published_at', '<=', now())
            ->orderByDesc('published_at')
            ->paginate(15);

        return view('parent.notices.index', compact('notices'));
    }

    public function exams(Request $request): View
    {
        $parent = auth()->user();
        $children = $parent->parentStudents()->with('classRoom')->get();

        $selectedChildId = $request->get('student_id');
        $examResults = collect();
        $exams = collect();

        if ($selectedChildId && $children->firstWhere('id', $selectedChildId)) {
            $student = $children->firstWhere('id', $selectedChildId);

            $exams = Exam::where('class_id', $student->class_id)
                ->with('academicYear')
                ->latest()
                ->get();

            $examResults = ExamResult::with(['exam', 'subject'])
                ->where('student_id', $selectedChildId)
                ->latest()
                ->get()
                ->groupBy('exam_id');
        }

        return view('parent.exams.index', compact('children', 'examResults', 'exams', 'selectedChildId'));
    }
}
