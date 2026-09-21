<?php

namespace App\Http\Controllers\Website;

use App\Enums\DayOfWeek;
use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\ClassRoutine;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassRoutineController extends Controller
{
    public function index(Request $request): View
    {
        return view('web.class-routine', $this->routineData($request));
    }

    public function grid(Request $request): View
    {
        return view('web.partials.class-routine-grid', $this->routineData($request));
    }

    private function routineData(Request $request): array
    {
        $classes = ClassRoom::all();

        $selectedClassId = $request->integer('class_id') ?: $classes->first()?->id;

        $slots = [];
        $grid = [];

        if ($selectedClassId) {
            $routines = ClassRoutine::with(['subject', 'teacher'])
                ->where('class_id', $selectedClassId)
                ->orderBy('start_time')
                ->get();

            foreach ($routines as $routine) {
                $slotKey = $routine->start_time->format('H:i').' - '.$routine->end_time->format('H:i');

                $slots[$slotKey] ??= [
                    'label' => $slotKey,
                    'start' => $routine->start_time->format('H:i'),
                ];

                if ($day = $routine->dayEnum()) {
                    $grid[$slotKey][$day->order()] = $routine;
                }
            }

            uasort($slots, fn (array $a, array $b): int => strcmp($a['start'], $b['start']));
        }

        return [
            'classes' => $classes,
            'selectedClassId' => $selectedClassId,
            'selectedClass' => $classes->firstWhere('id', $selectedClassId),
            'days' => DayOfWeek::weekdays(),
            'slots' => $slots,
            'grid' => $grid,
        ];
    }
}
