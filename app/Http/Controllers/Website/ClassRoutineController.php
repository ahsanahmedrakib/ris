<?php

namespace App\Http\Controllers\Website;

use App\Enums\DayOfWeek;
use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\ClassRoutine;
use App\Support\Seo;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassRoutineController extends Controller
{
    /**
     * The routine is one grid for every class, selected by a query parameter.
     * That parameter does not make a distinct page: /class-routine and
     * /class-routine?class_id=3 are the same document, so the canonical URL
     * drops it and the two URLs do not compete with each other.
     */
    public function index(Request $request): View
    {
        return view('web.class-routine', [
            ...$this->routineData($request),
            'seo' => Seo::forCurrentPage()->withCanonical(route('class-routine')),
        ]);
    }

    /**
     * The grid is also served on its own and swapped in over ajax. It carries no
     * head of its own, so it is given the same metadata object and left to render
     * without it.
     */
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
