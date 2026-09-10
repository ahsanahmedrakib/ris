<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(): View
    {
        $subjects = Subject::with(['classRoom', 'teacher'])->latest()->get();

        return view('admin.subjects.index', compact('subjects'));
    }

    public function create(): View
    {
        $classes = ClassRoom::orderBy('name')->get();
        $teachers = User::where('role', 'teacher')->where('is_active', true)->orderBy('name')->get();

        return view('admin.subjects.create', compact('classes', 'teachers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:subjects,code',
            'class_id' => 'required|exists:classes,id',
            'teacher_id' => 'required|exists:users,id',
        ], [
            'name.required' => 'বিষয়ের নাম আবশ্যক।',
            'name.string' => 'বিষয়ের নাম অবশ্যই একটি স্ট্রিং হতে হবে।',
            'code.required' => 'বিষয় কোড আবশ্যক।',
            'code.unique' => 'এই বিষয় কোড ইতিমধ্যে বিদ্যমান।',
            'class_id.required' => 'শ্রেণি নির্বাচন আবশ্যক।',
            'class_id.exists' => 'নির্বাচিত শ্রেণি বিদ্যমান নেই।',
            'teacher_id.required' => 'শিক্ষক নির্বাচন আবশ্যক।',
            'teacher_id.exists' => 'নির্বাচিত শিক্ষক বিদ্যমান নেই।',
        ]);

        try {
            Subject::create($validated);

            return redirect()->route('admin.subjects.index')
                ->with('success', 'বিষয় সফলভাবে যোগ করা হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'বিষয় যোগ করতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }

    public function edit(int $id): View
    {
        $subject = Subject::findOrFail($id);
        $classes = ClassRoom::orderBy('name')->get();
        $teachers = User::where('role', 'teacher')->where('is_active', true)->orderBy('name')->get();

        return view('admin.subjects.edit', compact('subject', 'classes', 'teachers'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $subject = Subject::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => "required|string|max:20|unique:subjects,code,{$subject->id}",
            'class_id' => 'required|exists:classes,id',
            'teacher_id' => 'required|exists:users,id',
        ], [
            'name.required' => 'বিষয়ের নাম আবশ্যক।',
            'code.required' => 'বিষয় কোড আবশ্যক।',
            'code.unique' => 'এই বিষয় কোড ইতিমধ্যে বিদ্যমান।',
            'class_id.required' => 'শ্রেণি নির্বাচন আবশ্যক।',
            'class_id.exists' => 'নির্বাচিত শ্রেণি বিদ্যমান নেই।',
            'teacher_id.required' => 'শিক্ষক নির্বাচন আবশ্যক।',
            'teacher_id.exists' => 'নির্বাচিত শিক্ষক বিদ্যমান নেই।',
        ]);

        try {
            $subject->update($validated);

            return redirect()->route('admin.subjects.index')
                ->with('success', 'বিষয় সফলভাবে আপডেট হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'বিষয় আপডেট করতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            Subject::findOrFail($id)->delete();

            return redirect()->route('admin.subjects.index')
                ->with('success', 'বিষয় সফলভাবে মুছে ফেলা হয়েছে।');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'বিষয় মুছে ফেলতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }
}
