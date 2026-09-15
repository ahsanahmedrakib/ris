<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(Request $request): View
    {
        $query = GalleryItem::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $items = $query->orderBy('sort_order')->orderByDesc('id')->paginate($perPage)->withQueryString();

        $categories = GalleryItem::pluck('category')->filter()->unique()->values();

        return view('admin.gallery.index', ['items' => $items, 'categories' => $categories]);
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('admin.gallery.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,jpg,png,webp|max:5120',
            'category' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'title.required' => 'শিরোনাম আবশ্যক।',
            'image.required' => 'ছবি আবশ্যক।',
            'image.image' => 'সঠিক ছবি আপলোড করুন।',
            'image.mimes' => 'ছবির ফরম্যাট jpeg, jpg, png বা webp হতে হবে।',
            'image.max' => 'ছবির আকার ৫ এমবির বেশি হতে পারবে না।',
        ]);

        try {
            $imagePath = $request->file('image')->store('gallery', 'public');

            GalleryItem::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'image' => $imagePath,
                'category' => $validated['category'] ?? null,
                'sort_order' => $validated['sort_order'] ?? 0,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            return redirect()->route('admin.gallery.index')
                ->with('success', 'গ্যালারি ছবি সফলভাবে যোগ করা হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'গ্যালারি ছবি যোগ করতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        $item = GalleryItem::findOrFail($id);

        return response()->json([
            'id' => $item->id,
            'title' => $item->title,
            'description' => $item->description,
            'image' => Storage::url($item->image),
            'category' => $item->category,
            'is_active' => $item->is_active,
            'status_label' => $item->is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়',
            'created_at' => $item->created_at->format('d/m/Y h:i A'),
        ]);
    }

    public function edit(int $id): JsonResponse
    {
        $item = GalleryItem::findOrFail($id);

        return response()->json([
            'id' => $item->id,
            'title' => $item->title,
            'description' => $item->description,
            'image' => Storage::url($item->image),
            'category' => $item->category,
            'is_active' => $item->is_active,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $item = GalleryItem::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'category' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'title.required' => 'শিরোনাম আবশ্যক।',
            'image.image' => 'সঠিক ছবি আপলোড করুন।',
            'image.mimes' => 'ছবির ফরম্যাট jpeg, jpg, png বা webp হতে হবে।',
            'image.max' => 'ছবির আকার ৫ এমবির বেশি হতে পারবে না।',
        ]);

        try {
            if ($request->hasFile('image')) {
                if ($item->image) {
                    Storage::disk('public')->delete($item->image);
                }

                $validated['image'] = $request->file('image')->store('gallery', 'public');
            }

            $validated['is_active'] = $validated['is_active'] ?? $item->is_active;
            $item->update($validated);

            return redirect()->route('admin.gallery.index')
                ->with('success', 'গ্যালারি ছবি সফলভাবে আপডেট হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'গ্যালারি ছবি আপডেট করতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }

    public function toggleActive(int $id): JsonResponse
    {
        $item = GalleryItem::findOrFail($id);
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
            GalleryItem::findOrFail($id)->delete();

            return redirect()->route('admin.gallery.index')
                ->with('success', 'গ্যালারি ছবি সফলভাবে মুছে ফেলা হয়েছে।');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'গ্যালারি ছবি মুছে ফেলতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }
}
