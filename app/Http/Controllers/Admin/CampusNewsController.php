<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CampusNews;
use App\Support\XlsxExport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CampusNewsController extends Controller
{
    public function index(Request $request): View
    {
        $query = CampusNews::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $items = $query->orderByDesc('id')->paginate($perPage)->withQueryString();

        return view('admin.campus-news.index', ['items' => $items]);
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('admin.campus-news.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'title.required' => 'শিরোনাম আবশ্যক।',
            'date.required' => 'তারিখ আবশ্যক।',
            'date.date' => 'সঠিক তারিখ দিন।',
            'image.image' => 'সঠিক ছবি আপলোড করুন।',
            'image.mimes' => 'ছবির ফরম্যাট jpeg, jpg, png বা webp হতে হবে।',
            'image.max' => 'ছবির আকার ৫ এমবির বেশি হতে পারবে না।',
        ]);

        try {
            $imagePath = $request->hasFile('image')
                ? $request->file('image')->store('campus-news', 'public')
                : null;

            CampusNews::create([
                'title' => $validated['title'],
                'date' => $validated['date'],
                'image' => $imagePath,
                'description' => $validated['description'] ?? null,
                'sort_order' => $validated['sort_order'] ?? 0,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            return redirect()->route('admin.campus-news.index')
                ->with('success', 'ক্যাম্পাস লাইফ সফলভাবে যোগ করা হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'ক্যাম্পাস লাইফ যোগ করতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        $item = CampusNews::findOrFail($id);

        return response()->json([
            'id' => $item->id,
            'title' => $item->title,
            'date' => $item->date?->format('Y-m-d'),
            'image' => $item->image ? Storage::url($item->image) : null,
            'description' => $item->description,
            'is_active' => $item->is_active,
            'status_label' => $item->is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়',
            'created_at' => $item->created_at->format('d/m/Y h:i A'),
        ]);
    }

    public function edit(int $id): JsonResponse
    {
        $item = CampusNews::findOrFail($id);

        return response()->json([
            'id' => $item->id,
            'title' => $item->title,
            'date' => $item->date?->format('Y-m-d'),
            'image' => $item->image ? Storage::url($item->image) : null,
            'description' => $item->description,
            'sort_order' => $item->sort_order,
            'is_active' => $item->is_active,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $item = CampusNews::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'title.required' => 'শিরোনাম আবশ্যক।',
            'date.required' => 'তারিখ আবশ্যক।',
            'date.date' => 'সঠিক তারিখ দিন।',
            'image.image' => 'সঠিক ছবি আপলোড করুন।',
            'image.mimes' => 'ছবির ফরম্যাট jpeg, jpg, png বা webp হতে হবে।',
            'image.max' => 'ছবির আকার ৫ এমবির বেশি হতে পারবে না।',
        ]);

        try {
            if ($request->hasFile('image')) {
                if ($item->image) {
                    Storage::disk('public')->delete($item->image);
                }

                $validated['image'] = $request->file('image')->store('campus-news', 'public');
            }

            $validated['is_active'] = $validated['is_active'] ?? $item->is_active;
            $item->update($validated);

            return redirect()->route('admin.campus-news.index')
                ->with('success', 'ক্যাম্পাস লাইফ সফলভাবে আপডেট হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'ক্যাম্পাস লাইফ আপডেট করতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }

    public function toggleActive(int $id): JsonResponse
    {
        $item = CampusNews::findOrFail($id);
        $item->is_active = ! $item->is_active;
        $item->save();

        return response()->json([
            'id' => $item->id,
            'is_active' => $item->is_active,
            'status_label' => $item->is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়',
        ]);
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            CampusNews::findOrFail($id)->delete();

            return redirect()->route('admin.campus-news.index')
                ->with('success', 'ক্যাম্পাস লাইফ সফলভাবে মুছে ফেলা হয়েছে।');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'ক্যাম্পাস লাইফ মুছে ফেলতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }

    public function downloadAll(Request $request)
    {
        $query = CampusNews::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $items = $query->latest('id')->get();

        $rows = $items->map(fn ($item, $index) => [
            $index + 1,
            $item->title,
            $item->date?->format('d/m/Y') ?? '-',
            strip_tags($item->description ?? ''),
            $item->is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়',
            $item->created_at->format('d/m/Y h:i A'),
        ])->all();

        return XlsxExport::download(
            ['ক্রমিক', 'শিরোনাম', 'তারিখ', 'বিবরণ', 'স্ট্যাটাস', 'তৈরির সময়'],
            $rows,
            'campus_news_'.now('Asia/Dhaka')->format('Y-m-d_H-i').'.xlsx',
        );
    }
}
