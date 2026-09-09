<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(): JsonResponse
    {
        $subjects = Subject::with(['classRoom', 'teacher'])->latest()->get();

        return response()->json($subjects);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:subjects,code',
            'class_id' => 'required|exists:classes,id',
            'teacher_id' => 'required|exists:users,id',
        ]);

        try {
            $subject = Subject::create($validated);

            return response()->json([
                'message' => 'বিষয় সফলভাবে যোগ করা হয়েছে।',
                'subject' => $subject->load(['classRoom', 'teacher']),
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'বিষয় যোগ করতে সমস্যা হয়েছে।'], 500);
        }
    }

    public function show($id): JsonResponse
    {
        $subject = Subject::with(['classRoom', 'teacher'])->findOrFail($id);

        return response()->json($subject);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $subject = Subject::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => "required|string|unique:subjects,code,{$subject->id}",
            'class_id' => 'required|exists:classes,id',
            'teacher_id' => 'required|exists:users,id',
        ]);

        try {
            $subject->update($validated);

            return response()->json([
                'message' => 'বিষয় সফলভাবে আপডেট হয়েছে।',
                'subject' => $subject->fresh()->load(['classRoom', 'teacher']),
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'বিষয় আপডেট করতে সমস্যা হয়েছে।'], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            Subject::findOrFail($id)->delete();

            return response()->json(['message' => 'বিষয় সফলভাবে মুছে ফেলা হয়েছে।']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'বিষয় মুছে ফেলতে সমস্যা হয়েছে।'], 500);
        }
    }
}
