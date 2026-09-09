<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NoticeController extends Controller
{
    public function index(): View
    {
        $notices = Notice::with('publisher')->latest('published_at')->paginate(15);

        return view('admin.notices.index', compact('notices'));
    }

    public function create(): View
    {
        return view('admin.notices.create');
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
            'expires_at.after_or_equal' => 'মেয়াদ শেষের তারিখ প্রকাশের তারিখের পরে হতে হবে।',
        ]);

        try {
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

    public function show(int $id): View
    {
        $notice = Notice::with('publisher')->findOrFail($id);

        return view('admin.notices.show', compact('notice'));
    }

    public function edit(int $id): View
    {
        $notice = Notice::findOrFail($id);

        return view('admin.notices.edit', compact('notice'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $notice = Notice::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|string|in:notice,event,holiday',
            'target_role' => 'nullable|string|in:admin,teacher,student,parent,all',
            'published_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:published_at',
            'is_active' => 'boolean',
        ], [
            'title.required' => 'বিজ্ঞপ্তির শিরোনাম আবশ্যক।',
            'content.required' => 'বিজ্ঞপ্তির বিষয়বস্তু আবশ্যক।',
            'type.required' => 'বিজ্ঞপ্তির ধরন আবশ্যক।',
            'type.in' => 'সঠিক বিজ্ঞপ্তির ধরন নির্বাচন করুন।',
        ]);

        try {
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
}
