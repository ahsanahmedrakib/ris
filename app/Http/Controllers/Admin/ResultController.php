<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Support\XlsxExport;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class ResultController extends Controller
{
    public function index(Request $request): View
    {
        $classes = ClassRoom::get();
        $exams = Exam::with('classRoom')->latest('start_date')->get();

        $classId = $request->input('class_id');
        $examId = $request->input('exam_id');

        $groups = $this->buildGroups($classId, $examId);

        return view('admin.results.index', compact('classes', 'exams', 'classId', 'examId', 'groups'));
    }

    public function export(Request $request): Response
    {
        $classId = $request->input('class_id');
        $examId = $request->input('exam_id');

        $rows = [];

        foreach ($this->buildGroups($classId, $examId) as $group) {
            $exam = $group['exam'];

            foreach ($group['students'] as $bean) {
                $student = $bean['student'];

                foreach ($group['subjects'] as $subject) {
                    $result = $bean['marks']->get($subject->id);

                    $rows[] = [
                        $exam->name,
                        $exam->classRoom?->name ?? '-',
                        $student->roll_no ?? '-',
                        $student->user?->name ?? '-',
                        $subject->name,
                        $result?->marks_obtained !== null ? (string) $result->marks_obtained : '-',
                        $exam->total_marks,
                        $result?->grade ?? '-',
                    ];
                }
            }
        }

        return XlsxExport::download(
            ['পরীক্ষা', 'শ্রেণি', 'রোল', 'শিক্ষার্থীর নাম', 'বিষয়', 'প্রাপ্ত নম্বর', 'পূর্ণমান', 'গ্রেড'],
            $rows,
            'all_results_'.now('Asia/Dhaka')->format('Y-m-d_H-i').'.xlsx',
        );
    }

    /**
     * @return Collection<int, array{exam: Exam, students: Collection, subjects: Collection}>
     */
    private function buildGroups(?string $classId, ?string $examId): Collection
    {
        $query = ExamResult::with(['student.user', 'subject', 'exam.classRoom']);

        if ($classId) {
            $query->whereHas('student', fn ($q) => $q->where('class_id', $classId));
        }

        if ($examId) {
            $query->where('exam_id', $examId);
        }

        return $query->latest('id')->get()
            ->groupBy('exam_id')
            ->map(function ($rows) {
                $exam = $rows->first()->exam;

                $students = $rows->groupBy('student_id')->mapWithKeys(function ($studentRows) {
                    $first = $studentRows->first();

                    return [$first->student_id => [
                        'student' => $first->student,
                        'marks' => $studentRows->keyBy('subject_id'),
                    ]];
                });

                $subjects = $rows->unique('subject_id')
                    ->map(fn ($row) => $row->subject)
                    ->sortBy('name')
                    ->values();

                return compact('exam', 'students', 'subjects');
            })
            ->sortByDesc(fn ($group) => $group['exam']->start_date?->toDateString())
            ->values();
    }
}
