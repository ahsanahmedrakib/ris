<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AttendanceStatus;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $query = Attendance::with(['student.user', 'classRoom', 'marker']);

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        $attendances = $query->latest('date')->paginate(20)->withQueryString();
        $classes = ClassRoom::orderBy('name')->get();

        return view('admin.attendance.index', compact('attendances', 'classes'));
    }

    public function create(Request $request): View
    {
        $classes = ClassRoom::orderBy('name')->get();
        $students = collect();
        $date = $request->get('date', today()->toDateString());

        if ($request->filled('class_id')) {
            $students = Student::where('class_id', $request->class_id)
                ->where('is_active', true)
                ->with('user')
                ->orderBy('roll_no')
                ->get();
        }

        $statuses = AttendanceStatus::cases();

        return view('admin.attendance.create', compact('classes', 'students', 'date', 'statuses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:students,id',
            'attendances.*.status' => 'required|in:present,absent,late,excused',
            'attendances.*.remarks' => 'nullable|string|max:255',
        ], [
            'class_id.required' => 'শ্রেণী নির্বাচন আবশ্যক।',
            'class_id.exists' => 'নির্বাচিত শ্রেণী বিদ্যমান নেই।',
            'date.required' => 'তারিখ আবশ্যক।',
            'attendances.required' => 'উপস্থিতি তালিকা আবশ্যক।',
            'attendances.array' => 'উপস্থিতি তালিকা অবশ্যই একটি অ্যারে হতে হবে।',
            'attendances.*.student_id.required' => 'ছাত্র/ছাত্রী নির্বাচন আবশ্যক।',
            'attendances.*.student_id.exists' => 'নির্বাচিত ছাত্র/ছাত্রী বিদ্যমান নেই।',
            'attendances.*.status.required' => 'উপস্থিতির অবস্থা আবশ্যক।',
            'attendances.*.status.in' => 'সঠিক উপস্থিতির অবস্থা নির্বাচন করুন।',
        ]);

        try {
            foreach ($validated['attendances'] as $attendance) {
                Attendance::updateOrCreate(
                    [
                        'student_id' => $attendance['student_id'],
                        'class_id' => $validated['class_id'],
                        'date' => $validated['date'],
                    ],
                    [
                        'status' => $attendance['status'],
                        'remarks' => $attendance['remarks'] ?? null,
                        'marked_by' => auth()->id(),
                    ]
                );
            }

            return redirect()->route('admin.attendance.index')
                ->with('success', 'উপস্থিতি সফলভাবে সংরক্ষিত হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'উপস্থিতি সংরক্ষণ করতে সমস্যা হয়েছে। ' . $e->getMessage());
        }
    }

    public function show($id): View
    {
        $attendance = Attendance::with(['student.user', 'classRoom', 'marker'])
            ->findOrFail($id);

        return view('admin.attendance.show', compact('attendance'));
    }

    public function edit($id): View
    {
        $attendance = Attendance::with('student')->findOrFail($id);
        $classes = ClassRoom::orderBy('name')->get();
        $statuses = AttendanceStatus::cases();

        return view('admin.attendance.edit', compact('attendance', 'classes', 'statuses'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $attendance = Attendance::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:present,absent,late,excused',
            'remarks' => 'nullable|string|max:255',
        ], [
            'status.required' => 'উপস্থিতির অবস্থা আবশ্যক।',
            'status.in' => 'সঠিক উপস্থিতির অবস্থা নির্বাচন করুন।',
        ]);

        try {
            $attendance->update($validated);

            return redirect()->route('admin.attendance.index')
                ->with('success', 'উপস্থিতি সফলভাবে আপডেট হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'উপস্থিতি আপডেট করতে সমস্যা হয়েছে। ' . $e->getMessage());
        }
    }

    public function destroy($id): RedirectResponse
    {
        try {
            Attendance::findOrFail($id)->delete();

            return redirect()->route('admin.attendance.index')
                ->with('success', 'উপস্থিতি সফলভাবে মুছে ফেলা হয়েছে।');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'উপস্থিতি মুছে ফেলতে সমস্যা হয়েছে। ' . $e->getMessage());
        }
    }
}
