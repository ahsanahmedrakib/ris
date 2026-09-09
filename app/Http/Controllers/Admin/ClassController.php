<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ClassController extends Controller
{
    public function index(): View
    {
        $classes = ClassRoom::with(['academicYear', 'classTeacher'])
            ->withCount('students')
            ->orderBy('name')
            ->get();

        return view('admin.classes.index', compact('classes'));
    }

    public function create(): View
    {
        $academicYears = AcademicYear::orderByDesc('is_current')->orderByDesc('name')->get();
        $teachers = User::where('role', 'teacher')->where('is_active', true)->orderBy('name')->get();

        return view('admin.classes.create', compact('academicYears', 'teachers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'section' => 'nullable|string|max:10',
            'academic_year_id' => 'required|exists:academic_years,id',
            'class_teacher_id' => 'nullable|exists:users,id',
        ], [
            'name.required' => 'শ্রেণীর নাম আবশ্যক।',
            'name.string' => 'শ্রেণীর নাম অবশ্যই একটি স্ট্রিং হতে হবে।',
            'academic_year_id.required' => 'শিক্ষাবর্ষ নির্বাচন আবশ্যক।',
            'academic_year_id.exists' => 'নির্বাচিত শিক্ষাবর্ষ বিদ্যমান নেই।',
            'class_teacher_id.exists' => 'নির্বাচিত শিক্ষক বিদ্যমান নেই।',
        ]);

        try {
            ClassRoom::create($validated);

            return redirect()->route('admin.classes.index')
                ->with('success', 'শ্রেণী সফলভাবে তৈরি হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'শ্রেণী তৈরি করতে সমস্যা হয়েছে। ' . $e->getMessage());
        }
    }

    public function show($id): View
    {
        $class = ClassRoom::with([
            'academicYear',
            'classTeacher',
            'students' => fn ($q) => $q->with('user')->orderBy('roll_no'),
            'subjects' => fn ($q) => $q->with('teacher'),
        ])->withCount('students')->findOrFail($id);

        return view('admin.classes.show', compact('class'));
    }

    public function edit($id): View
    {
        $class = ClassRoom::findOrFail($id);
        $academicYears = AcademicYear::orderByDesc('is_current')->orderByDesc('name')->get();
        $teachers = User::where('role', 'teacher')->where('is_active', true)->orderBy('name')->get();

        return view('admin.classes.edit', compact('class', 'academicYears', 'teachers'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $class = ClassRoom::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'section' => 'nullable|string|max:10',
            'academic_year_id' => 'required|exists:academic_years,id',
            'class_teacher_id' => 'nullable|exists:users,id',
        ], [
            'name.required' => 'শ্রেণীর নাম আবশ্যক।',
            'academic_year_id.required' => 'শিক্ষাবর্ষ নির্বাচন আবশ্যক।',
            'academic_year_id.exists' => 'নির্বাচিত শিক্ষাবর্ষ বিদ্যমান নেই।',
            'class_teacher_id.exists' => 'নির্বাচিত শিক্ষক বিদ্যমান নেই।',
        ]);

        try {
            $class->update($validated);

            return redirect()->route('admin.classes.index')
                ->with('success', 'শ্রেণী সফলভাবে আপডেট হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'শ্রেণী আপডেট করতে সমস্যা হয়েছে। ' . $e->getMessage());
        }
    }

    public function destroy($id): RedirectResponse
    {
        try {
            $class = ClassRoom::findOrFail($id);

            if ($class->students()->count() > 0) {
                return back()
                    ->with('error', 'এই শ্রেণীতে ছাত্র/ছাত্রী আছে, তাই এটি মুছে ফেলা যাচ্ছে না।');
            }

            $class->delete();

            return redirect()->route('admin.classes.index')
                ->with('success', 'শ্রেণী সফলভাবে মুছে ফেলা হয়েছে।');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'শ্রেণী মুছে ফেলতে সমস্যা হয়েছে। ' . $e->getMessage());
        }
    }
}
