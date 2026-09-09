<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index(): JsonResponse
    {
        $classes = ClassRoom::with(['academicYear', 'classTeacher'])->withCount('students')->orderBy('name')->get();

        return response()->json($classes);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'section' => 'nullable|string|max:10',
            'academic_year_id' => 'required|exists:academic_years,id',
            'class_teacher_id' => 'nullable|exists:users,id',
        ]);

        try {
            $class = ClassRoom::create($validated);

            return response()->json([
                'message' => 'শ্রেণী সফলভাবে তৈরি হয়েছে।',
                'class' => $class->load(['academicYear', 'classTeacher']),
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'শ্রেণী তৈরি করতে সমস্যা হয়েছে।'], 500);
        }
    }

    public function show($id): JsonResponse
    {
        $class = ClassRoom::with(['academicYear', 'classTeacher', 'students' => fn ($q) => $q->with('user')])->withCount('students')->findOrFail($id);

        return response()->json($class);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $class = ClassRoom::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        try {
            $class->update($validated);

            return response()->json([
                'message' => 'শ্রেণী সফলভাবে আপডেট হয়েছে।',
                'class' => $class->fresh()->load(['academicYear', 'classTeacher']),
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'শ্রেণী আপডেট করতে সমস্যা হয়েছে।'], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            ClassRoom::findOrFail($id)->delete();

            return response()->json(['message' => 'শ্রেণী সফলভাবে মুছে ফেলা হয়েছে।']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'শ্রেণী মুছে ফেলতে সমস্যা হয়েছে।'], 500);
        }
    }
}
