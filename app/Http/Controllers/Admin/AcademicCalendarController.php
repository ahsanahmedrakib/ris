<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicCalendar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AcademicCalendarController extends Controller
{
    public function index(Request $request): View
    {
        $query = AcademicCalendar::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('year', 'like', "%{$search}%");
            });
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $calendars = $query->orderByDesc('year')->orderBy('sort_order')->paginate($perPage)->withQueryString();

        $years = AcademicCalendar::pluck('year')->filter()->unique()->sortDesc()->values();

        return view('admin.academic-calendars.index', ['calendars' => $calendars, 'years' => $years]);
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('admin.academic-calendars.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:pdf|max:10240',
            'year' => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'title.required' => 'শিরোনাম আবশ্যক।',
            'file.required' => 'পিডিএফ ফাইল আবশ্যক।',
            'file.mimes' => 'শুধুমাত্র পিডিএফ ফাইল আপলোড করা যাবে।',
            'file.max' => 'ফাইলের আকার ১০ এমবির বেশি হতে পারবে না।',
        ]);

        try {
            $filePath = $request->file('file')->store('academic-calendars', 'public');

            AcademicCalendar::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'file_path' => $filePath,
                'year' => $validated['year'] ?? null,
                'sort_order' => $validated['sort_order'] ?? 0,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            return redirect()->route('admin.academic-calendars.index')
                ->with('success', 'একাডেমিক ক্যালেন্ডার সফলভাবে আপলোড হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'একাডেমিক ক্যালেন্ডার আপলোড করতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        $calendar = AcademicCalendar::findOrFail($id);

        return response()->json([
            'id' => $calendar->id,
            'title' => $calendar->title,
            'description' => $calendar->description,
            'file_url' => Storage::url($calendar->file_path),
            'file_name' => $calendar->file_name,
            'year' => $calendar->year,
            'sort_order' => (string) $calendar->sort_order,
            'is_active' => $calendar->is_active,
            'status_label' => $calendar->is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়',
            'created_at' => $calendar->created_at->format('d/m/Y h:i A'),
        ]);
    }

    public function edit(int $id): JsonResponse
    {
        $calendar = AcademicCalendar::findOrFail($id);

        return response()->json([
            'id' => $calendar->id,
            'title' => $calendar->title,
            'description' => $calendar->description,
            'file_url' => Storage::url($calendar->file_path),
            'file_name' => $calendar->file_name,
            'year' => $calendar->year,
            'sort_order' => (string) $calendar->sort_order,
            'is_active' => $calendar->is_active,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $calendar = AcademicCalendar::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf|max:10240',
            'year' => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'title.required' => 'শিরোনাম আবশ্যক।',
            'file.mimes' => 'শুধুমাত্র পিডিএফ ফাইল আপলোড করা যাবে।',
            'file.max' => 'ফাইলের আকার ১০ এমবির বেশি হতে পারবে না।',
        ]);

        try {
            if ($request->hasFile('file')) {
                if ($calendar->file_path) {
                    Storage::disk('public')->delete($calendar->file_path);
                }

                $validated['file_path'] = $request->file('file')->store('academic-calendars', 'public');
            }

            $validated['is_active'] = $validated['is_active'] ?? $calendar->is_active;
            unset($validated['file']);
            $calendar->update($validated);

            return redirect()->route('admin.academic-calendars.index')
                ->with('success', 'একাডেমিক ক্যালেন্ডার সফলভাবে আপডেট হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'একাডেমিক ক্যালেন্ডার আপডেট করতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }

    public function toggleActive(int $id): JsonResponse
    {
        $calendar = AcademicCalendar::findOrFail($id);
        $calendar->is_active = ! $calendar->is_active;
        $calendar->save();

        return response()->json([
            'id' => $calendar->id,
            'is_active' => $calendar->is_active,
            'status_label' => $calendar->is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়',
        ]);
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            AcademicCalendar::findOrFail($id)->delete();

            return redirect()->route('admin.academic-calendars.index')
                ->with('success', 'একাডেমিক ক্যালেন্ডার সফলভাবে মুছে ফেলা হয়েছে।');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'একাডেমিক ক্যালেন্ডার মুছে ফেলতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }
}
