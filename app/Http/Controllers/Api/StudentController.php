<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Student::with(['classRoom', 'user']);

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('admission_no', 'like', "%{$search}%")
                    ->orWhere('guardian_name', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        $students = $query->latest()->paginate(15);

        return response()->json($students);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'admission_no' => 'required|string|unique:students,admission_no',
            'class_id' => 'required|exists:classes,id',
            'section' => 'required|string|max:10',
            'roll_no' => 'required|integer|min:0',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'address' => 'required|string',
            'guardian_name' => 'required|string|max:255',
            'guardian_phone' => 'required|string|max:20',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => bcrypt(Str::random(10)),
                'role' => UserRole::Student->value,
                'is_active' => true,
            ]);

            $student = Student::create(array_merge($validated, [
                'user_id' => $user->id,
                'is_active' => true,
            ]));

            DB::commit();

            return response()->json([
                'message' => 'ছাত্র/ছাত্রী সফলভাবে যোগ করা হয়েছে।',
                'student' => $student->load(['user', 'classRoom']),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'ছাত্র/ছাত্রী যোগ করতে সমস্যা হয়েছে।'], 500);
        }
    }

    public function show($id): JsonResponse
    {
        $student = Student::with(['user', 'classRoom', 'parents', 'attendances', 'examResults' => fn ($q) => $q->with('exam', 'subject')])->findOrFail($id);

        return response()->json($student);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $student = Student::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$student->user_id}",
            'class_id' => 'required|exists:classes,id',
            'section' => 'nullable|string|max:10',
            'roll_no' => 'nullable|integer|min:0',
            'address' => 'nullable|string',
            'guardian_name' => 'required|string|max:255',
            'guardian_phone' => 'required|string|max:20',
        ]);

        try {
            if ($student->user) {
                $student->user->update(['name' => $validated['name'], 'email' => $validated['email']]);
            }
            $student->update($validated);

            return response()->json([
                'message' => 'ছাত্র/ছাত্রী সফলভাবে আপডেট হয়েছে।',
                'student' => $student->fresh()->load(['user', 'classRoom']),
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'ছাত্র/ছাত্রী আপডেট করতে সমস্যা হয়েছে।'], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $student = Student::findOrFail($id);
            if ($student->user) {
                $student->user->update(['is_active' => false]);
            }
            $student->update(['is_active' => false]);

            return response()->json(['message' => 'ছাত্র/ছাত্রী সফলভাবে মুছে ফেলা হয়েছে।']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'ছাত্র/ছাত্রী মুছে ফেলতে সমস্যা হয়েছে।'], 500);
        }
    }
}
