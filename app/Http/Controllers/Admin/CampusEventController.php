<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CampusEvent;
use App\Support\Media;
use App\Support\XlsxExport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CampusEventController extends Controller
{
    public function index(Request $request): View
    {
        $query = CampusEvent::query();

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

        return view('admin.campus-events.index', ['items' => $items]);
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('admin.campus-events.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->normalizeSlugInput($request);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('campus_events', 'slug')],
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
            'slug.unique' => 'এই ঠিকানাটি (slug) ইতিমধ্যে ব্যবহার করা হয়েছে।',
        ]);

        try {
            $imagePath = $request->hasFile('image')
                ? Media::storeImage($request->file('image'), 'campus-events')
                : null;

            CampusEvent::create([
                'title' => $validated['title'],
                // Left out when the field was blank, so the model assigns a
                // serial address (campus-event-{id}) rather than an empty one.
                ...(filled($validated['slug'] ?? null) ? ['slug' => $validated['slug']] : []),
                'date' => $validated['date'],
                'image' => $imagePath,
                'description' => $validated['description'] ?? null,
                'sort_order' => $validated['sort_order'] ?? 0,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            return redirect()->route('admin.campus-events.index')
                ->with('success', 'ক্যাম্পাস ইভেন্ট সফলভাবে যোগ করা হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'ক্যাম্পাস ইভেন্ট যোগ করতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }

    /**
     * Normalises the optional slug before validation, so uniqueness is checked
     * against the address a visitor will land on rather than the raw input. An
     * empty field becomes null because a unique index counts an empty string as
     * a value, which would reject the second blank one.
     */
    protected function normalizeSlugInput(Request $request): void
    {
        if (! $request->has('slug')) {
            return;
        }

        $slug = Str::slug((string) $request->input('slug'));

        $request->merge(['slug' => $slug === '' ? null : $slug]);
    }

    public function show(int $id): JsonResponse
    {
        $item = CampusEvent::findOrFail($id);

        return response()->json([
            'id' => $item->id,
            'title' => $item->title,
            'date' => $item->date?->format('Y-m-d'),
            'image' => $item->image ? Media::images()->url($item->image) : null,
            'description' => $item->description,
            'is_active' => $item->is_active,
            'status_label' => $item->is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়',
            'created_at' => $item->created_at->format('d/m/Y h:i A'),
        ]);
    }

    public function edit(int $id): JsonResponse
    {
        $item = CampusEvent::findOrFail($id);

        return response()->json([
            'id' => $item->id,
            'title' => $item->title,
            'slug' => $item->slug,
            'date' => $item->date?->format('Y-m-d'),
            'image' => $item->image ? Media::images()->url($item->image) : null,
            'description' => $item->description,
            'sort_order' => $item->sort_order,
            'is_active' => $item->is_active,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $item = CampusEvent::findOrFail($id);

        $this->normalizeSlugInput($request);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('campus_events', 'slug')->ignore($item->id)],
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
            'slug.unique' => 'এই ঠিকানাটি (slug) ইতিমধ্যে ব্যবহার করা হয়েছে।',
        ]);

        try {
            if ($request->hasFile('image')) {
                if ($item->image) {
                    Storage::disk('public')->delete($item->image);
                }

                $validated['image'] = Media::storeImage($request->file('image'), 'campus-events');
            }

            $validated['is_active'] = $validated['is_active'] ?? $item->is_active;

            // A blank field leaves the published address alone rather than
            // wiping a link that has already been shared.
            if (blank($validated['slug'] ?? null)) {
                unset($validated['slug']);
            }

            $item->update($validated);

            return redirect()->route('admin.campus-events.index')
                ->with('success', 'ক্যাম্পাস ইভেন্ট সফলভাবে আপডেট হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'ক্যাম্পাস ইভেন্ট আপডেট করতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }

    public function toggleActive(int $id): JsonResponse
    {
        $item = CampusEvent::findOrFail($id);
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
            CampusEvent::findOrFail($id)->delete();

            return redirect()->route('admin.campus-events.index')
                ->with('success', 'ক্যাম্পাস ইভেন্ট সফলভাবে মুছে ফেলা হয়েছে।');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'ক্যাম্পাস ইভেন্ট মুছে ফেলতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }

    public function downloadAll(Request $request)
    {
        $query = CampusEvent::query();

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
            'campus_events_'.now('Asia/Dhaka')->format('Y-m-d_H-i').'.xlsx',
        );
    }
}
