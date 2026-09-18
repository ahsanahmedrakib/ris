<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutContent;
use App\Models\CoreValue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function index(): View
    {
        $mission = AboutContent::where('type', 'mission')->first();
        $vision = AboutContent::where('type', 'vision')->first();
        $coreValues = CoreValue::orderBy('sort_order')->orderBy('id')->get();

        return view('admin.about.index', compact('mission', 'vision', 'coreValues'));
    }

    public function storeMissionVision(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:mission,vision',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ], [
            'type.required' => 'ধরন নির্বাচন আবশ্যক।',
            'type.in' => 'সঠিক ধরন নির্বাচন করুন।',
            'title.required' => 'শিরোনাম আবশ্যক।',
            'content.required' => 'বিবরণ আবশ্যক।',
        ]);

        try {
            if (AboutContent::where('type', $validated['type'])->exists()) {
                return back()
                    ->with('error', 'এই ধরণের তথ্য আগে থেকেই যোগ করা আছে। সম্পাদনা করুন।');
            }

            AboutContent::create($validated);
            $label = $validated['type'] === 'mission' ? 'মিশন' : 'ভিশন';

            return back()->with('success', $label.' সফলভাবে যোগ করা হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'তথ্য যোগ করতে সমস্যা হয়েছে. '.$e->getMessage());
        }
    }

    public function updateMissionVision(Request $request, int $id): RedirectResponse
    {
        $record = AboutContent::findOrFail($id);

        $validated = $request->validate([
            'type' => 'required|in:mission,vision',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ], [
            'type.required' => 'ধরন নির্বাচন আবশ্যক।',
            'type.in' => 'সঠিক ধরন নির্বাচন করুন।',
            'title.required' => 'শিরোনাম আবশ্যক।',
            'content.required' => 'বিবরণ আবশ্যক।',
        ]);

        try {
            $record->update($validated);
            $label = $validated['type'] === 'mission' ? 'মিশন' : 'ভিশন';

            return back()->with('success', $label.' সফলভাবে আপডেট করা হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'তথ্য আপডেট করতে সমস্যা হয়েছে. '.$e->getMessage());
        }
    }

    public function destroyMissionVision(int $id): RedirectResponse
    {
        try {
            $record = AboutContent::findOrFail($id);
            $label = $record->type === 'mission' ? 'মিশন' : 'ভিশন';
            $record->forceDelete();

            return back()->with('success', $label.' সফলভাবে মুছে ফেলা হয়েছে।');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'তথ্য মুছে ফেলতে সমস্যা হয়েছে. '.$e->getMessage());
        }
    }

    public function storeCoreValue(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'title.required' => 'মূল্যবোধের নাম আবশ্যক।',
            'description.required' => 'বিবরণ আবশ্যক।',
        ]);

        try {
            CoreValue::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'sort_order' => $validated['sort_order'] ?? 0,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            return back()->with('success', 'মূল্যবোধ সফলভাবে যোগ করা হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'মূল্যবোধ যোগ করতে সমস্যা হয়েছে. '.$e->getMessage());
        }
    }

    public function editCoreValue(int $id): JsonResponse
    {
        $coreValue = CoreValue::findOrFail($id);

        return response()->json([
            'id' => $coreValue->id,
            'title' => $coreValue->title,
            'description' => $coreValue->description,
            'sort_order' => (string) $coreValue->sort_order,
            'is_active' => $coreValue->is_active,
        ]);
    }

    public function updateCoreValue(Request $request, int $id): RedirectResponse
    {
        $coreValue = CoreValue::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'title.required' => 'মূল্যবোধের নাম আবশ্যক।',
            'description.required' => 'বিবরণ আবশ্যক।',
        ]);

        try {
            $validated['is_active'] = $validated['is_active'] ?? $coreValue->is_active;
            $coreValue->update($validated);

            return back()->with('success', 'মূল্যবোধ সফলভাবে আপডেট করা হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'মূল্যবোধ আপডেট করতে সমস্যা হয়েছে. '.$e->getMessage());
        }
    }

    public function toggleCoreValue(int $id): JsonResponse
    {
        $coreValue = CoreValue::findOrFail($id);
        $coreValue->is_active = ! $coreValue->is_active;
        $coreValue->save();

        return response()->json([
            'id' => $coreValue->id,
            'is_active' => $coreValue->is_active,
            'status_label' => $coreValue->is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়',
        ]);
    }

    public function destroyCoreValue(int $id): RedirectResponse
    {
        try {
            CoreValue::findOrFail($id)->delete();

            return back()->with('success', 'মূল্যবোধ সফলভাবে মুছে ফেলা হয়েছে।');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'মূল্যবোধ মুছে ফেলতে সমস্যা হয়েছে. '.$e->getMessage());
        }
    }
}
