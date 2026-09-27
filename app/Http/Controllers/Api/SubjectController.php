<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\SubjectResource;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectController extends Controller
{
    public function index(): JsonResource
    {
        $subjects = Subject::with(['classRoom', 'teacher'])->latest()->get();

        return SubjectResource::collection($subjects);
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
                'subject' => new SubjectResource($subject->load(['classRoom', 'teacher'])),
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'বিষয় যোগ করতে সমস্যা হয়েছে।'], 500);
        }
    }

    public function show(int $id): JsonResource
    {
        $subject = Subject::with(['classRoom', 'teacher'])->findOrFail($id);

        return new SubjectResource($subject);
    }

    public function update(Request $request, int $id): JsonResponse
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
                'subject' => new SubjectResource($subject->fresh()->load(['classRoom', 'teacher'])),
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'বিষয় আপডেট করতে সমস্যা হয়েছে।'], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            Subject::findOrFail($id)->delete();

            return response()->json(['message' => 'বিষয় সফলভাবে মুছে ফেলা হয়েছে।']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'বিষয় মুছে ফেলতে সমস্যা হয়েছে।'], 500);
        }
    }
}
