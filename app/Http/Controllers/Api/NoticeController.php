<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    public function index(): JsonResponse
    {
        $notices = Notice::with('publisher')->latest('published_at')->paginate(15);

        return response()->json($notices);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:general,academic,exam,fee,event',
            'target_role' => 'nullable|in:admin,teacher,student,parent,all',
            'published_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:published_at',
            'is_active' => 'boolean',
        ]);

        try {
            $validated['published_by'] = auth()->id();
            $validated['published_at'] = $validated['published_at'] ?? now();
            $validated['is_active'] = $validated['is_active'] ?? true;

            $notice = Notice::create($validated);

            return response()->json([
                'message' => 'বিজ্ঞপ্তি সফলভাবে প্রকাশিত হয়েছে।',
                'notice' => $notice->load('publisher'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'বিজ্ঞপ্তি প্রকাশ করতে সমস্যা হয়েছে।'], 500);
        }
    }

    public function show($id): JsonResponse
    {
        $notice = Notice::with('publisher')->findOrFail($id);

        return response()->json($notice);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $notice = Notice::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:general,academic,exam,fee,event',
        ]);

        try {
            $notice->update($validated);

            return response()->json([
                'message' => 'বিজ্ঞপ্তি সফলভাবে আপডেট হয়েছে।',
                'notice' => $notice->fresh()->load('publisher'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'বিজ্ঞপ্তি আপডেট করতে সমস্যা হয়েছে।'], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            Notice::findOrFail($id)->delete();

            return response()->json(['message' => 'বিজ্ঞপ্তি সফলভাবে মুছে ফেলা হয়েছে।']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'বিজ্ঞপ্তি মুছে ফেলতে সমস্যা হয়েছে।'], 500);
        }
    }
}
