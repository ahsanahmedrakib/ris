<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamResult;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Exam::with(['classRoom', 'academicYear']);

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        $exams = $query->latest()->paginate(15);

        return response()->json($exams);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:quiz,midterm,final,assignment',
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'total_marks' => 'required|integer|min:1',
            'passing_marks' => 'required|integer|min:1|lte:total_marks',
        ]);

        try {
            $exam = Exam::create($validated);

            return response()->json([
                'message' => 'পরীক্ষা সফলভাবে তৈরি হয়েছে।',
                'exam' => $exam->load(['classRoom', 'academicYear']),
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'পরীক্ষা তৈরি করতে সমস্যা হয়েছে।'], 500);
        }
    }

    public function show($id): JsonResponse
    {
        $exam = Exam::with(['classRoom', 'academicYear', 'examResults' => fn ($q) => $q->with(['student.user', 'subject'])])->findOrFail($id);

        return response()->json($exam);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $exam = Exam::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:quiz,midterm,final,assignment',
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'total_marks' => 'required|integer|min:1',
            'passing_marks' => 'required|integer|min:1|lte:total_marks',
        ]);

        try {
            $exam->update($validated);

            return response()->json([
                'message' => 'পরীক্ষা সফলভাবে আপডেট হয়েছে।',
                'exam' => $exam->fresh()->load(['classRoom', 'academicYear']),
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'পরীক্ষা আপডেট করতে সমস্যা হয়েছে।'], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            Exam::findOrFail($id)->delete();

            return response()->json(['message' => 'পরীক্ষা সফলভাবে মুছে ফেলা হয়েছে।']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'পরীক্ষা মুছে ফেলতে সমস্যা হয়েছে।'], 500);
        }
    }
}
