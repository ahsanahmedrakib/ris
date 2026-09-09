<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Attendance::with(['student.user', 'classRoom', 'marker']);

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        $attendances = $query->latest('date')->paginate(20);

        return response()->json($attendances);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:classes,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent,late,excused',
            'remarks' => 'nullable|string|max:255',
        ]);

        try {
            $attendance = Attendance::updateOrCreate(
                ['student_id' => $validated['student_id'], 'class_id' => $validated['class_id'], 'date' => $validated['date']],
                ['status' => $validated['status'], 'remarks' => $validated['remarks'] ?? null, 'marked_by' => Auth::id()]
            );

            return response()->json([
                'message' => 'উপস্থিতি সফলভাবে সংরক্ষিত হয়েছে।',
                'attendance' => $attendance->load(['student.user', 'classRoom']),
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'উপস্থিতি সংরক্ষণ করতে সমস্যা হয়েছে।'], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        $attendance = Attendance::with(['student.user', 'classRoom', 'marker'])->findOrFail($id);

        return response()->json($attendance);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $attendance = Attendance::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:present,absent,late,excused',
            'remarks' => 'nullable|string|max:255',
        ]);

        try {
            $attendance->update($validated);

            return response()->json([
                'message' => 'উপস্থিতি সফলভাবে আপডেট হয়েছে।',
                'attendance' => $attendance->fresh()->load(['student.user', 'classRoom']),
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'উপস্থিতি আপডেট করতে সমস্যা হয়েছে।'], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            Attendance::findOrFail($id)->delete();

            return response()->json(['message' => 'উপস্থিতি সফলভাবে মুছে ফেলা হয়েছে।']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'উপস্থিতি মুছে ফেলতে সমস্যা হয়েছে।'], 500);
        }
    }
}
