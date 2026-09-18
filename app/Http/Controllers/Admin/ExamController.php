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
use App\Support\NumberConverter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $query = Exam::with(['classRoom', 'academicYear']);

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        $exams = $query->latest()->paginate(10)->withQueryString();
        $classes = ClassRoom::get();
        $academicYears = AcademicYear::forSessionDropdown();
        $examTypes = ExamType::cases();

        return view('admin.exams.index', compact('exams', 'classes', 'academicYears', 'examTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'total_marks' => NumberConverter::toAscii($request->input('total_marks')),
            'passing_marks' => NumberConverter::toAscii($request->input('passing_marks')),
        ]);

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
            'class_id.required' => 'শ্রেণি নির্বাচন আবশ্যক।',
            'class_id.exists' => 'নির্বাচিত শ্রেণি বিদ্যমান নেই।',
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
                ->with('error', 'পরীক্ষা তৈরি করতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        $exam = Exam::with(['classRoom', 'academicYear'])->findOrFail($id);

        return response()->json([
            'id' => $exam->id,
            'name' => $exam->name,
            'type' => $exam->type,
            'type_label' => ExamType::tryFrom($exam->type)?->label() ?? $exam->type,
            'class_id' => $exam->class_id,
            'class_name' => $exam->classRoom?->name ?? '-',
            'academic_year_id' => $exam->academic_year_id,
            'academic_year_name' => $exam->academicYear?->yearLabel() ?? '-',
            'start_date' => $exam->start_date?->format('d/m/Y') ?? '-',
            'end_date' => $exam->end_date?->format('d/m/Y') ?? '-',
            'total_marks' => $exam->total_marks,
            'passing_marks' => $exam->passing_marks,
            'created_at' => $exam->created_at->format('d/m/Y h:i A'),
        ]);
    }

    public function edit(int $id): JsonResponse
    {
        $exam = Exam::findOrFail($id);

        return response()->json([
            'id' => $exam->id,
            'name' => $exam->name ?? '',
            'type' => $exam->type ?? '',
            'class_id' => (string) $exam->class_id,
            'academic_year_id' => (string) $exam->academic_year_id,
            'start_date' => $exam->start_date?->format('d/m/Y') ?? '',
            'end_date' => $exam->end_date?->format('d/m/Y') ?? '',
            'total_marks' => (string) ($exam->total_marks ?? ''),
            'passing_marks' => (string) ($exam->passing_marks ?? ''),
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $exam = Exam::findOrFail($id);

        $request->merge([
            'total_marks' => NumberConverter::toAscii($request->input('total_marks')),
            'passing_marks' => NumberConverter::toAscii($request->input('passing_marks')),
        ]);

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
            'class_id.required' => 'শ্রেণি নির্বাচন আবশ্যক।',
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
                ->with('error', 'পরীক্ষা আপডেট করতে সমস্যা হয়েছে। '.$e->getMessage());
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
                ->with('error', 'পরীক্ষা মুছে ফেলতে সমস্যা হয়েছে। '.$e->getMessage());
        }
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
            ->keyBy(fn ($r) => "{$r->student_id}_{$r->subject_id}");

        $marks = $students->mapWithKeys(function (Student $student) use ($subjects, $existingResults): array {
            $subjectMarks = $subjects->mapWithKeys(function (Subject $subject) use ($student, $existingResults): array {
                $existing = $existingResults["{$student->id}_{$subject->id}"] ?? null;

                return [$subject->id => $existing ? (string) $existing->marks_obtained : ''];
            });

            return [$student->id => $subjectMarks->toArray()];
        })->toArray();

        return view('admin.exams.results', compact('exam', 'students', 'subjects', 'marks'));
    }

    public function storeResults(Request $request, int $id): RedirectResponse
    {
        $exam = Exam::findOrFail($id);

        if (is_array($request->input('result'))) {
            $request->merge([
                'result' => collect($request->input('result'))
                    ->map(fn ($subjectMarks) => array_map(
                        fn ($marks) => NumberConverter::toAscii($marks),
                        $subjectMarks
                    ))
                    ->all(),
            ]);
        }

        $validated = $request->validate([
            'result' => 'nullable|array',
            'result.*' => 'array',
            'result.*.*' => 'nullable|numeric|min:0|max:'.$exam->total_marks,
        ], [
            'result.*.*.numeric' => 'প্রাপ্ত নম্বর অবশ্যই একটি সংখ্যা হতে হবে।',
            'result.*.*.min' => 'প্রাপ্ত নম্বর ০ এর কম হতে পারবে না।',
            'result.*.*.max' => 'প্রাপ্ত নম্বর মোট নম্বরের বেশি হতে পারবে না।',
        ]);

        try {
            $rows = $validated['result'] ?? [];
            $saved = 0;

            foreach ($rows as $studentId => $subjectMarks) {
                foreach ($subjectMarks as $subjectId => $marks) {
                    if ($marks === null || $marks === '') {
                        continue;
                    }

                    $student = Student::find($studentId);
                    $subject = Subject::find($subjectId);

                    if (! $student || $student->class_id !== $exam->class_id) {
                        continue;
                    }

                    if (! $subject || $subject->class_id !== $exam->class_id) {
                        continue;
                    }

                    ExamResult::updateOrCreate(
                        [
                            'exam_id' => $id,
                            'student_id' => (int) $studentId,
                            'subject_id' => (int) $subjectId,
                        ],
                        [
                            'marks_obtained' => (float) $marks,
                            'grade' => ExamResult::calculateGrade((float) $marks, (float) $exam->total_marks, (float) $exam->passing_marks),
                            'entered_by' => Auth::id(),
                        ]
                    );

                    $saved++;
                }
            }

            if ($saved === 0) {
                return back()->with('error', 'কোনো নম্বর প্রদান করা হয়নি।');
            }

            return redirect()->route('admin.exams.results', $exam)
                ->with('success', 'পরীক্ষার ফলাফল সফলভাবে সংরক্ষিত হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'ফলাফল সংরক্ষণ করতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }
}
