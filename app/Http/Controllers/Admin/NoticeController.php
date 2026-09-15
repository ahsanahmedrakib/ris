<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Support\XlsxExport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NoticeController extends Controller
{
    public function index(Request $request): View
    {
        $notices = Notice::with('publisher')
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->category))
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->where('title', 'like', "%{$request->search}%")
                        ->orWhere('content', 'like', "%{$request->search}%");
                });
            })
            ->latest('published_at')
            ->paginate(10);

        return view('admin.notices.index', [
            'notices' => $notices,
            'categories' => Notice::CATEGORIES,
        ]);
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('admin.notices.index');
    }

    public function store(Request $request): RedirectResponse
    {
        if ($request->missing('published_at')) {
            $request->merge(['published_at' => now()]);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|string|in:notice,event,holiday',
            'category' => 'nullable|string|in:admission,exam,holiday,general',
            'target_role' => 'nullable|string|in:admin,teacher,student,parent,all',
            'published_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:published_at',
            'is_active' => 'boolean',
        ], [
            'title.required' => 'বিজ্ঞপ্তির শিরোনাম আবশ্যক।',
            'title.string' => 'শিরোনাম অবশ্যই একটি স্ট্রিং হতে হবে।',
            'content.required' => 'বিজ্ঞপ্তির বিষয়বস্তু আবশ্যক।',
            'type.required' => 'বিজ্ঞপ্তির ধরন আবশ্যক।',
            'type.in' => 'সঠিক বিজ্ঞপ্তির ধরন নির্বাচন করুন।',
            'category.in' => 'সঠিক বিজ্ঞপ্তির ক্যাটাগরি নির্বাচন করুন।',
            'expires_at.after_or_equal' => 'মেয়াদ শেষের তারিখ প্রকাশের তারিখের পরে হতে হবে।',
        ]);

        try {
            $validated['category'] = $validated['category'] ?? 'general';
            $validated['published_by'] = Auth::id();
            $validated['published_at'] = $validated['published_at'] ?? now();
            $validated['is_active'] = $validated['is_active'] ?? true;

            Notice::create($validated);

            return redirect()->route('admin.notices.index')
                ->with('success', 'বিজ্ঞপ্তি সফলভাবে প্রকাশিত হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'বিজ্ঞপ্তি প্রকাশ করতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        $notice = Notice::with('publisher')->findOrFail($id);

        return response()->json([
            'id' => $notice->id,
            'title' => $notice->title,
            'content' => $notice->content,
            'type' => $notice->type,
            'type_label' => $notice->type === 'event' ? 'অনুষ্ঠান' : ($notice->type === 'holiday' ? 'ছুটি' : 'নোটিশ'),
            'category' => $notice->category,
            'category_label' => Notice::CATEGORIES[$notice->category] ?? 'সাধারণ',
            'target_role' => $notice->target_role,
            'target_label' => $notice->target_role === 'all' ? 'সকল' : ($notice->target_role === 'teacher' ? 'শিক্ষক' : ($notice->target_role === 'parent' ? 'অভিভাবক' : ($notice->target_role === 'admin' ? 'অ্যাডমিন' : 'ছাত্র'))),
            'published_at' => $notice->published_at?->format('d/m/Y h:i A') ?? '-',
            'expires_at' => $notice->expires_at?->format('d/m/Y h:i A') ?? '-',
            'is_active' => $notice->is_active,
            'status_label' => $notice->is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়',
            'publisher_name' => $notice->publisher?->name ?? '-',
            'created_at' => $notice->created_at?->format('d/m/Y h:i A'),
        ]);
    }

    public function edit(int $id): JsonResponse
    {
        $notice = Notice::findOrFail($id);

        return response()->json([
            'id' => $notice->id,
            'title' => $notice->title,
            'content' => $notice->content,
            'type' => $notice->type,
            'category' => $notice->category ?? 'general',
            'target_role' => $notice->target_role ?? 'all',
            'published_at' => $notice->published_at?->format('Y-m-d\TH:i'),
            'expires_at' => $notice->expires_at?->format('Y-m-d\TH:i'),
            'is_active' => (bool) $notice->is_active,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $notice = Notice::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|string|in:notice,event,holiday',
            'category' => 'nullable|string|in:admission,exam,holiday,general',
            'target_role' => 'nullable|string|in:admin,teacher,student,parent,all',
            'published_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:published_at',
            'is_active' => 'boolean',
        ], [
            'title.required' => 'বিজ্ঞপ্তির শিরোনাম আবশ্যক।',
            'content.required' => 'বিজ্ঞপ্তির বিষয়বস্তু আবশ্যক।',
            'type.required' => 'বিজ্ঞপ্তির ধরন আবশ্যক।',
            'type.in' => 'সঠিক বিজ্ঞপ্তির ধরন নির্বাচন করুন।',
            'category.in' => 'সঠিক বিজ্ঞপ্তির ক্যাটাগরি নির্বাচন করুন।',
        ]);

        try {
            $validated['category'] = $validated['category'] ?? $notice->category ?? 'general';
            $notice->update($validated);

            return redirect()->route('admin.notices.index')
                ->with('success', 'বিজ্ঞপ্তি সফলভাবে আপডেট হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'বিজ্ঞপ্তি আপডেট করতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            Notice::findOrFail($id)->delete();

            return redirect()->route('admin.notices.index')
                ->with('success', 'বিজ্ঞপ্তি সফলভাবে মুছে ফেলা হয়েছে।');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'বিজ্ঞপ্তি মুছে ফেলতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }

    public function toggleActive(int $id): JsonResponse
    {
        $notice = Notice::findOrFail($id);
        $notice->is_active = ! $notice->is_active;
        $notice->save();

        return response()->json([
            'id' => $notice->id,
            'is_active' => $notice->is_active,
            'status_label' => $notice->is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়',
        ]);
    }

    public function downloadAll(Request $request)
    {
        $notices = Notice::with('publisher')
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->category))
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->where('title', 'like', "%{$request->search}%")
                        ->orWhere('content', 'like', "%{$request->search}%");
                });
            })
            ->latest('published_at')
            ->get();

        $rows = $notices->map(fn ($notice, $index) => [
            $index + 1,
            $notice->title,
            Notice::CATEGORIES[$notice->category] ?? $notice->category ?? 'সাধারণ',
            $notice->type === 'event' ? 'অনুষ্ঠান' : ($notice->type === 'holiday' ? 'ছুটি' : 'নোটিশ'),
            $notice->target_role === 'all' ? 'সকল' : ($notice->target_role === 'teacher' ? 'শিক্ষক' : ($notice->target_role === 'parent' ? 'অভিভাবক' : 'ছাত্র')),
            strip_tags($notice->content),
            $notice->published_at?->format('d/m/Y H:i') ?? '-',
            $notice->is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়',
        ])->all();

        return XlsxExport::download(
            ['ক্রমিক', 'শিরোনাম', 'ক্যাটাগরি', 'ধরন', 'লক্ষ্য', 'বিবরণ', 'প্রকাশের তারিখ', 'স্ট্যাটাস'],
            $rows,
            'notices_'.now('Asia/Dhaka')->format('Y-m-d_H-i').'.xlsx',
        );
    }
}
