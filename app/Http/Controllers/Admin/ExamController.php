<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ExamType;
use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    public function index(Request $request): View
    {
        $query = Exam::with(['classRoom', 'academicYear']);

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        $exams = $query->latest()->paginate(15)->withQueryString();
        $classes = ClassRoom::orderBy('name')->get();

        return view('admin.exams.index', compact('exams', 'classes'));
    }

    public function create(): View
    {
        $classes = ClassRoom::orderBy('name')->get();
        $academicYears = AcademicYear::orderByDesc('is_current')->orderByDesc('name')->get();
        $examTypes = ExamType::cases();

        return view('admin.exams.create', compact('classes', 'academicYears', 'examTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:quiz,midterm,final,assignment',
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'total_marks' => 'required|integer|min:1',
            'passing_marks' => 'required|integer|min:1|lte:total_marks',
        ], [
            'name.required' => 'পরীক্ষার নাম আবশ্যক।',
            'type.required' => 'পরীক্ষার ধরন আবশ্যক।',
            'type.in' => 'সঠিক পরীক্ষার ধরন নির্বাচন করুন।',
            'class_id.required' => 'শ্রেণী নির্বাচন আবশ্যক।',
            'class_id.exists' => 'নির্বাচিত শ্রেণী বিদ্যমান নেই।',
            'academic_year_id.required' => 'শিক্ষাবর্ষ নির্বাচন আবশ্যক।',
            'academic_year_id.exists' => 'নির্বাচিত শিক্ষাবর্ষ বিদ্যমান নেই।',
            'start_date.required' => 'শুরুর তারিখ আবশ্যক।',
            'end_date.required' => 'শেষ তারিখ আবশ্যক।',
            'end_date.after_or_equal' => 'শেষ তারিখ শুরুর তারিখের সমান বা পরে হতে হবে।',
            'total_marks.required' => 'মোট নম্বর আবশ্যক।',
            'total_marks.integer' => 'মোট নম্বর অবশ্যই একটি পূর্ণসংখ্যা হতে হবে।',
            'passing_marks.required' => 'পাসের নম্বর আবশ্যক।',
            'passing_marks.lte' => 'পাসের নম্বর মোট নম্বরের সমান বা কম হতে হবে।',
        ]);

        try {
            Exam::create($validated);

            return redirect()->route('admin.exams.index')
                ->with('success', 'পরীক্ষা সফলভাবে তৈরি হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'পরীক্ষা তৈরি করতে সমস্যা হয়েছে। ' . $e->getMessage());
        }
    }

    public function show(int $id): View
    {
        $exam = Exam::with([
            'classRoom',
            'academicYear',
            'examResults' => fn($q) => $q->with(['student.user', 'subject'])->orderBy('student_id'),
        ])->withCount('examResults')->findOrFail($id);

        return view('admin.exams.show', compact('exam'));
    }

    public function results(int $id): View
    {
        $exam = Exam::with(['classRoom', 'academicYear'])->findOrFail($id);
        $students = Student::where('class_id', $exam->class_id)
            ->where('is_active', true)
            ->with('user')
            ->orderBy('roll_no')
            ->get();
        $subjects = Subject::where('class_id', $exam->class_id)->get();
        $existingResults = ExamResult::where('exam_id', $id)
            ->get()
            ->keyBy(fn($r) => "{$r->student_id}_{$r->subject_id}");

        return view('admin.exams.results', compact('exam', 'students', 'subjects', 'existingResults'));
    }

    public function storeResults(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'results' => 'required|array',
            'results.*.student_id' => 'required|exists:students,id',
            'results.*.subject_id' => 'required|exists:subjects,id',
            'results.*.marks_obtained' => 'required|numeric|min:0',
            'results.*.grade' => 'nullable|string|max:5',
            'results.*.remarks' => 'nullable|string|max:255',
        ], [
            'results.required' => 'ফলাফল তালিকা আবশ্যক।',
            'results.array' => 'ফলাফল তালিকা অবশ্যই একটি অ্যারে হতে হবে।',
            'results.*.student_id.required' => 'ছাত্র/ছাত্রী নির্বাচন আবশ্যক।',
            'results.*.student_id.exists' => 'নির্বাচিত ছাত্র/ছাত্রী বিদ্যমান নেই।',
            'results.*.subject_id.required' => 'বিষয় নির্বাচন আবশ্যক।',
            'results.*.subject_id.exists' => 'নির্বাচিত বিষয় বিদ্যমান নেই।',
            'results.*.marks_obtained.required' => 'প্রাপ্ত নম্বর আবশ্যক।',
            'results.*.marks_obtained.numeric' => 'প্রাপ্ত নম্বর অবশ্যই একটি সংখ্যা হতে হবে।',
        ]);

        try {
            foreach ($validated['results'] as $result) {
                ExamResult::updateOrCreate(
                    [
                        'exam_id' => $id,
                        'student_id' => $result['student_id'],
                        'subject_id' => $result['subject_id'],
                    ],
                    [
                        'marks_obtained' => $result['marks_obtained'],
                        'grade' => $result['grade'] ?? null,
                        'remarks' => $result['remarks'] ?? null,
                        'entered_by' => Auth::id(),
                    ]
                );
            }

            return redirect()->route('admin.exams.show', $id)
                ->with('success', 'পরীক্ষার ফলাফল সফলভাবে সংরক্ষিত হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'ফলাফল সংরক্ষণ করতে সমস্যা হয়েছে। ' . $e->getMessage());
        }
    }

    public function edit(int $id): View
    {
        $exam = Exam::findOrFail($id);
        $classes = ClassRoom::orderBy('name')->get();
        $academicYears = AcademicYear::orderByDesc('is_current')->orderByDesc('name')->get();
        $examTypes = ExamType::cases();

        return view('admin.exams.edit', compact('exam', 'classes', 'academicYears', 'examTypes'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $exam = Exam::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:quiz,midterm,final,assignment',
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'total_marks' => 'required|integer|min:1',
            'passing_marks' => 'required|integer|min:1|lte:total_marks',
        ], [
            'name.required' => 'পরীক্ষার নাম আবশ্যক।',
            'type.required' => 'পরীক্ষার ধরন আবশ্যক।',
            'class_id.required' => 'শ্রেণী নির্বাচন আবশ্যক।',
            'academic_year_id.required' => 'শিক্ষাবর্ষ নির্বাচন আবশ্যক।',
            'start_date.required' => 'শুরুর তারিখ আবশ্যক।',
            'end_date.required' => 'শেষ তারিখ আবশ্যক।',
            'total_marks.required' => 'মোট নম্বর আবশ্যক।',
            'passing_marks.required' => 'পাসের নম্বর আবশ্যক।',
            'passing_marks.lte' => 'পাসের নম্বর মোট নম্বরের সমান বা কম হতে হবে।',
        ]);

        try {
            $exam->update($validated);

            return redirect()->route('admin.exams.index')
                ->with('success', 'পরীক্ষা সফলভাবে আপডেট হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'পরীক্ষা আপডেট করতে সমস্যা হয়েছে। ' . $e->getMessage());
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            Exam::findOrFail($id)->delete();

            return redirect()->route('admin.exams.index')
                ->with('success', 'পরীক্ষা সফলভাবে মুছে ফেলা হয়েছে।');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'পরীক্ষা মুছে ফেলতে সমস্যা হয়েছে। ' . $e->getMessage());
        }
    }
}
