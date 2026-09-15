<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
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
                    ->orWhere('guardian_phone', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->boolean('status'));
        }

        $students = $query->latest()->paginate(10)->withQueryString();
        $classes = ClassRoom::orderBy('name')->get();

        return view('admin.students.index', compact('students', 'classes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'admission_no' => 'required|string|unique:students,admission_no',
            'class_id' => 'required|exists:classes,id',
            'section' => 'nullable|string|max:10',
            'roll_no' => 'nullable|integer|min:1',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|string|in:male,female,other',
            'blood_group' => 'nullable|string|max:5',
            'address' => 'nullable|string|max:500',
            'guardian_name' => 'required|string|max:255',
            'guardian_phone' => 'required|string|max:20',
            'guardian_email' => 'nullable|email',
        ], [
            'name.required' => 'নাম আবশ্যক।',
            'name.string' => 'নাম অবশ্যই একটি স্ট্রিং হতে হবে।',
            'name.max' => 'নাম ২৫৫ অক্ষরের বেশি হতে পারবে না।',
            'email.required' => 'ইমেইল আবশ্যক।',
            'email.email' => 'সঠিক ইমেইল দিন।',
            'email.unique' => 'এই ইমেইল ইতিমধ্যে ব্যবহৃত হয়েছে।',
            'admission_no.required' => 'ভর্তি নম্বর আবশ্যক।',
            'admission_no.unique' => 'এই ভর্তি নম্বর ইতিমধ্যে বিদ্যমান।',
            'class_id.required' => 'শ্রেণি নির্বাচন আবশ্যক।',
            'class_id.exists' => 'নির্বাচিত শ্রেণি বিদ্যমান নেই।',
            'date_of_birth.required' => 'জন্ম তারিখ আবশ্যক।',
            'date_of_birth.before' => 'জন্ম তারিখ আজকের আগে হতে হবে।',
            'gender.required' => 'লিঙ্গ নির্বাচন আবশ্যক।',
            'gender.in' => 'সঠিক লিঙ্গ নির্বাচন করুন।',
            'guardian_name.required' => 'অভিভাবকের নাম আবশ্যক।',
            'guardian_phone.required' => 'অভিভাবকের ফোন নম্বর আবশ্যক।',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'password' => bcrypt(Str::random(10)),
                'role' => UserRole::Student->value,
                'is_active' => true,
            ]);

            Student::create(array_merge($validated, [
                'user_id' => $user->id,
                'is_active' => true,
            ]));

            DB::commit();

            return redirect()->route('admin.students.index')
                ->with('success', 'ছাত্র/ছাত্রী সফলভাবে যোগ করা হয়েছে।');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'ছাত্র/ছাত্রী যোগ করতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        $student = Student::with(['user', 'classRoom'])->findOrFail($id);

        $attendances = Attendance::where('student_id', $student->id)
            ->get('status')
            ->countBy('status');

        $present = (int) ($attendances['present'] ?? 0);
        $absent = (int) ($attendances['absent'] ?? 0);
        $late = (int) ($attendances['late'] ?? 0);
        $total = $present + $absent + $late;

        return response()->json([
            'id' => $student->id,
            'admission_no' => $student->admission_no,
            'name' => $student->user?->name,
            'email' => $student->user?->email,
            'phone' => $student->user?->phone,
            'photo' => $student->user?->avatar ? Storage::url($student->user->avatar) : null,
            'class' => $student->classRoom?->name,
            'section' => $student->section,
            'roll_no' => $student->roll_no,
            'date_of_birth' => $student->date_of_birth?->format('d/m/Y'),
            'gender' => match ($student->gender) {
                'male' => 'পুরুষ',
                'female' => 'মহিলা',
                'other' => 'অন্যান্য',
                default => null,
            },
            'blood_group' => $student->blood_group,
            'address' => $student->address,
            'guardian_name' => $student->guardian_name,
            'guardian_phone' => $student->guardian_phone,
            'guardian_email' => $student->guardian_email,
            'is_active' => (bool) $student->is_active,
            'attendance_summary' => compact('present', 'absent', 'late', 'total'),
            'created_at' => $student->created_at?->format('d/m/Y h:i A'),
        ]);
    }

    public function edit(int $id): JsonResponse
    {
        $student = Student::with('user')->findOrFail($id);

        return response()->json([
            'id' => $student->id,
            'name' => $student->user?->name ?? '',
            'email' => $student->user?->email ?? '',
            'phone' => $student->user?->phone ?? '',
            'photo' => $student->user?->avatar ? Storage::url($student->user->avatar) : null,
            'admission_no' => $student->admission_no ?? '',
            'class_id' => $student->class_id ?? '',
            'section' => $student->section ?? '',
            'roll_no' => $student->roll_no ?? '',
            'date_of_birth' => $student->date_of_birth?->format('d/m/Y') ?? '',
            'gender' => $student->gender ?? '',
            'blood_group' => $student->blood_group ?? '',
            'address' => $student->address ?? '',
            'guardian_name' => $student->guardian_name ?? '',
            'guardian_phone' => $student->guardian_phone ?? '',
            'guardian_email' => $student->guardian_email ?? '',
            'is_active' => (bool) $student->is_active,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $student = Student::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$student->user_id}",
            'phone' => 'nullable|string|max:20',
            'admission_no' => "required|string|unique:students,admission_no,{$student->id}",
            'class_id' => 'required|exists:classes,id',
            'section' => 'nullable|string|max:10',
            'roll_no' => 'nullable|integer|min:1',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|string|in:male,female,other',
            'blood_group' => 'nullable|string|max:5',
            'address' => 'nullable|string|max:500',
            'guardian_name' => 'required|string|max:255',
            'guardian_phone' => 'required|string|max:20',
            'guardian_email' => 'nullable|email',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'নাম আবশ্যক।',
            'email.required' => 'ইমেইল আবশ্যক।',
            'email.email' => 'সঠিক ইমেইল দিন।',
            'email.unique' => 'এই ইমেইল ইতিমধ্যে ব্যবহৃত হয়েছে।',
            'admission_no.required' => 'ভর্তি নম্বর আবশ্যক।',
            'admission_no.unique' => 'এই ভর্তি নম্বর ইতিমধ্যে বিদ্যমান।',
            'class_id.required' => 'শ্রেণি নির্বাচন আবশ্যক।',
            'class_id.exists' => 'নির্বাচিত শ্রেণি বিদ্যমান নেই।',
            'date_of_birth.required' => 'জন্ম তারিখ আবশ্যক।',
            'date_of_birth.before' => 'জন্ম তারিখ আজকের আগে হতে হবে।',
            'gender.required' => 'লিঙ্গ নির্বাচন আবশ্যক।',
            'gender.in' => 'সঠিক লিঙ্গ নির্বাচন করুন।',
            'guardian_name.required' => 'অভিভাবকের নাম আবশ্যক।',
            'guardian_phone.required' => 'অভিভাবকের ফোন নম্বর আবশ্যক।',
        ]);

        try {
            DB::beginTransaction();

            $student->user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            $student->update($validated);

            DB::commit();

            return redirect()->route('admin.students.index')
                ->with('success', 'ছাত্র/ছাত্রী তথ্য সফলভাবে আপডেট হয়েছে।');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'ছাত্র/ছাত্রী আপডেট করতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $student = Student::findOrFail($id);
            $student->user?->update(['is_active' => false]);
            $student->delete();

            return redirect()->route('admin.students.index')
                ->with('success', 'ছাত্র/ছাত্রী সফলভাবে ট্র্যাশে পাঠানো হয়েছে।');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'ছাত্র/ছাত্রী মুছে ফেলতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }

    public function toggleActive(int $id): JsonResponse
    {
        $student = Student::with('user')->findOrFail($id);
        $student->is_active = ! $student->is_active;
        $student->save();

        $student->user?->update(['is_active' => $student->is_active]);

        return response()->json([
            'id' => $student->id,
            'is_active' => $student->is_active,
            'status_label' => $student->is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়',
        ]);
    }
}
