<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(Request $request): View
    {
        $query = Faq::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('question', 'like', "%{$search}%")
                    ->orWhere('answer', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $faqs = $query->orderBy('sort_order')->orderBy('id')->paginate($perPage)->withQueryString();

        return view('admin.faqs.index', ['faqs' => $faqs]);
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('admin.faqs.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'question.required' => 'প্রশ্ন আবশ্যক।',
            'answer.required' => 'উত্তর আবশ্যক।',
        ]);

        try {
            Faq::create([
                'question' => $validated['question'],
                'answer' => $validated['answer'],
                'sort_order' => $validated['sort_order'] ?? 0,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            return redirect()->route('admin.faqs.index')
                ->with('success', 'প্রশ্নোত্তর সফলভাবে যোগ করা হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'প্রশ্নোত্তর যোগ করতে সমস্যা হয়েছে. '.$e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        $faq = Faq::findOrFail($id);

        return response()->json([
            'id' => $faq->id,
            'question' => $faq->question,
            'answer' => $faq->answer,
            'is_active' => $faq->is_active,
            'status_label' => $faq->is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়',
            'created_at' => $faq->created_at->format('d/m/Y h:i A'),
        ]);
    }

    public function edit(int $id): JsonResponse
    {
        $faq = Faq::findOrFail($id);

        return response()->json([
            'id' => $faq->id,
            'question' => $faq->question,
            'answer' => $faq->answer,
            'sort_order' => (string) $faq->sort_order,
            'is_active' => $faq->is_active,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $faq = Faq::findOrFail($id);

        $validated = $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'question.required' => 'প্রশ্ন আবশ্যক।',
            'answer.required' => 'উত্তর আবশ্যক।',
        ]);

        try {
            $validated['is_active'] = $validated['is_active'] ?? $faq->is_active;
            $faq->update($validated);

            return redirect()->route('admin.faqs.index')
                ->with('success', 'প্রশ্নোত্তর সফলভাবে আপডেট হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'প্রশ্নোত্তর আপডেট করতে সমস্যা হয়েছে. '.$e->getMessage());
        }
    }

    public function toggleActive(int $id): JsonResponse
    {
        $faq = Faq::findOrFail($id);
        $faq->is_active = ! $faq->is_active;
        $faq->save();

        return response()->json([
            'id' => $faq->id,
            'is_active' => $faq->is_active,
            'status_label' => $faq->is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়',
        ]);
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            Faq::findOrFail($id)->delete();

            return redirect()->route('admin.faqs.index')
                ->with('success', 'প্রশ্নোত্তর সফলভাবে মুছে ফেলা হয়েছে।');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'প্রশ্নোত্তর মুছে ফেলতে সমস্যা হয়েছে. '.$e->getMessage());
        }
    }
}
