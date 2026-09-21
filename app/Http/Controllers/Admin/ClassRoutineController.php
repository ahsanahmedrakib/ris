<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DayOfWeek;
use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\ClassRoutine;
use App\Models\Subject;
use App\Models\User;
use App\Support\XlsxExport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ClassRoutineController extends Controller
{
    public function index(Request $request): View
    {
        $classId = $request->integer('class_id') ?: null;

        $routines = ClassRoutine::with(['classRoom', 'subject', 'teacher'])
            ->when($classId, fn ($query) => $query->where('class_id', $classId))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.class-routine.index', [
            'routines' => $routines,
            'classes' => ClassRoom::orderBy('sort_order')->get(),
            'subjects' => Subject::all(),
            'teachers' => User::where('role', 'teacher')->where('is_active', true)->get(),
            'days' => DayOfWeek::weekdays(),
            'classId' => $classId,
        ]);
    }

    public function download(Request $request): Response
    {
        $classId = $request->integer('class_id') ?: null;

        $routines = ClassRoutine::with(['classRoom', 'subject', 'teacher'])
            ->when($classId, fn ($query) => $query->where('class_id', $classId))
            ->orderBy('class_id')
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $rows = $routines->map(fn (ClassRoutine $routine, int $index) => [
            $index + 1,
            $routine->classRoom?->name ?? '-',
            $routine->dayLabel() ?? '-',
            $routine->start_time?->format('H:i') ?? '-',
            $routine->end_time?->format('H:i') ?? '-',
            $routine->subject?->name ?? '-',
            $routine->teacher?->name ?? '-',
            $routine->room_no ?? '-',
        ])->all();

        $suffix = $classId ? '_class_'.$classId : '';

        return XlsxExport::download(
            ['ক্রমিক', 'শ্রেণি', 'দিন', 'শুরু', 'শেষ', 'বিষয়', 'শিক্ষক', 'কক্ষ'],
            $rows,
            'class_routines'.$suffix.'_'.now('Asia/Dhaka')->format('Y-m-d_H-i').'.xlsx',
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules(), $this->messages());

        try {
            ClassRoutine::create($this->payload($validated));

            return redirect()->route('admin.class-routines.index')
                ->with('success', 'ক্লাশ রুটিন সফলভাবে তৈরি হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'ক্লাশ রুটিন তৈরি করতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }

    public function show(ClassRoutine $classRoutine)
    {
        $classRoutine->load(['classRoom', 'subject', 'teacher']);

        return response()->json([
            'id' => $classRoutine->id,
            'class_name' => $classRoutine->classRoom?->name ?? '-',
            'subject_name' => $classRoutine->subject?->name ?? '-',
            'teacher_name' => $classRoutine->teacher?->name ?? '-',
            'day_label' => $classRoutine->dayLabel() ?? '-',
            'start_time' => $classRoutine->start_time?->format('H:i'),
            'end_time' => $classRoutine->end_time?->format('H:i'),
            'room_no' => $classRoutine->room_no ?? '-',
            'created_at' => $classRoutine->created_at?->format('d/m/Y h:i A'),
        ]);
    }

    public function edit(ClassRoutine $classRoutine)
    {
        return response()->json([
            'id' => $classRoutine->id,
            'class_id' => $classRoutine->class_id,
            'subject_id' => $classRoutine->subject_id,
            'teacher_id' => $classRoutine->teacher_id,
            'day_of_week' => $classRoutine->dayEnum()?->value ?? '',
            'start_time' => $classRoutine->start_time?->format('H:i'),
            'end_time' => $classRoutine->end_time?->format('H:i'),
            'room_no' => $classRoutine->room_no ?? '',
        ]);
    }

    public function update(Request $request, ClassRoutine $classRoutine): RedirectResponse
    {
        $validated = $request->validate($this->rules(), $this->messages());

        try {
            $classRoutine->update($this->payload($validated));

            return redirect()->route('admin.class-routines.index')
                ->with('success', 'ক্লাশ রুটিন সফলভাবে আপডেট হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'ক্লাশ রুটিন আপডেট করতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }

    public function destroy(ClassRoutine $classRoutine): RedirectResponse
    {
        try {
            $classRoutine->delete();

            return redirect()->route('admin.class-routines.index')
                ->with('success', 'ক্লাশ রুটিন মুছে ফেলা হয়েছে।');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'ক্লাশ রুটিন মুছে ফেলতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }

    /** @return array<string, mixed> */
    private function rules(): array
    {
        $days = array_map(fn (DayOfWeek $day) => $day->value, DayOfWeek::weekdays());

        return [
            'class_id' => ['required', 'exists:classes,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'teacher_id' => ['required', 'exists:users,id'],
            'day_of_week' => ['required', Rule::in($days)],
            'start_time' => ['required'],
            'end_time' => ['required'],
            'room_no' => ['nullable', 'string', 'max:50'],
        ];
    }

    /** @return array<string, string> */
    private function messages(): array
    {
        return [
            'class_id.required' => 'শ্রেণি নির্বাচন আবশ্যক।',
            'subject_id.required' => 'বিষয় নির্বাচন আবশ্যক।',
            'teacher_id.required' => 'শিক্ষক নির্বাচন আবশ্যক।',
            'day_of_week.required' => 'দিন নির্বাচন আবশ্যক।',
            'day_of_week.in' => 'শুক্রবার ছাড়া সঠিক দিন নির্বাচন করুন।',
            'start_time.required' => 'শুরু সময় আবশ্যক।',
            'end_time.required' => 'শেষ সময় আবশ্যক।',
        ];
    }

    /** @param array<string, mixed> $validated */
    private function payload(array $validated): array
    {
        return [
            'class_id' => $validated['class_id'],
            'subject_id' => $validated['subject_id'],
            'teacher_id' => $validated['teacher_id'],
            'day_of_week' => DayOfWeek::from($validated['day_of_week'])->order(),
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'room_no' => $validated['room_no'] ?? null,
        ];
    }
}
