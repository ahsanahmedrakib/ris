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
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CampusEventController extends Controller
{
    /**
     * Gallery size limits, in the units each rule counts in: ten items and
     * five mebibytes per image, one hundred megabytes for the optional video.
     */
    protected const MAX_IMAGES = 10;

    protected const MAX_IMAGE_KILOBYTES = 5120;

    protected const MAX_VIDEO_KILOBYTES = 102400;

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
            'images' => 'required|array|min:1|max:'.self::MAX_IMAGES,
            'images.*' => 'required|image|mimes:jpeg,jpg,png,webp|max:'.self::MAX_IMAGE_KILOBYTES,
            'video' => $this->videoRules(),
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'title.required' => 'শিরোনাম আবশ্যক।',
            'date.required' => 'তারিখ আবশ্যক।',
            'date.date' => 'সঠিক তারিখ দিন।',
            'images.required' => 'কমপক্ষে একটি ছবি আবশ্যক।',
            'images.array' => 'সর্বোচ্চ '.self::MAX_IMAGES.'টি ছবি যোগ করা যাবে।',
            'images.min' => 'কমপক্ষে একটি ছবি আবশ্যক।',
            'images.*.image' => 'সঠিক ছবি আপলোড করুন।',
            'images.*.mimes' => 'ছবির ফরম্যাট jpeg, jpg, png বা webp হতে হবে।',
            'images.*.max' => 'ছবির আকার ৫ এমবির বেশি হতে পারবে না।',
            'slug.unique' => 'এই ঠিকানাটি (slug) ইতিমধ্যে ব্যবহার করা হয়েছে।',
        ] + $this->videoMessages());

        $storedImages = [];
        $storedVideo = null;

        try {
            $storedImages = $this->storeUploads($request, 'images', 'image', 'campus-events');
            $storedVideo = $request->hasFile('video')
                ? Media::storeFile($request->file('video'), 'campus-events')
                : null;

            CampusEvent::create([
                'title' => $validated['title'],
                // Left out when the field was blank, so the model assigns a
                // serial address (campus-event-{id}) rather than an empty one.
                ...(filled($validated['slug'] ?? null) ? ['slug' => $validated['slug']] : []),
                'date' => $validated['date'],
                // The cover is the first image of the gallery, so every card,
                // thumbnail and schema that reads `image` keeps working.
                'image' => $storedImages[0],
                'images' => $storedImages,
                'video' => $storedVideo,
                'description' => $validated['description'] ?? null,
                'sort_order' => $validated['sort_order'] ?? 0,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            return redirect()->route('admin.campus-events.index')
                ->with('success', 'ক্যাম্পাস ইভেন্ট সফলভাবে যোগ করা হয়েছে।');
        } catch (\Exception $e) {
            $this->deleteUploads($storedImages, 'public');
            $this->deleteUploads(array_filter([$storedVideo]), 'public_files');

            return back()->withInput()
                ->with('error', 'ক্যাম্পাস ইভেন্ট যোগ করতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }

    /**
     * The rules shared by both write endpoints. `mimetypes` is used for the
     * video rather than `mimes` because the extension a client claims proves
     * nothing, and the guessed extension for Ogg video lands on `ogv`, which
     * would reject a file the field otherwise accepts.
     */
    protected function videoRules(): string
    {
        return 'nullable|file|mimetypes:video/mp4,video/webm,video/ogg,video/quicktime|max:'.self::MAX_VIDEO_KILOBYTES;
    }

    /**
     * @return array<string, string>
     */
    protected function videoMessages(): array
    {
        return [
            'video.file' => 'সঠিক ভিডিও ফাইল আপলোড করুন।',
            'video.mimetypes' => 'ভিডিওর ফরম্যাট MP4, WEBM, OGG বা MOV হতে হবে।',
            'video.max' => 'ভিডিওর আকার ১০০ এমবির বেশি হতে পারবে না।',
        ];
    }

    /**
     * Store every upload under a validated field and hand back their paths in
     * the order the client sent them.
     *
     * @return list<string>
     */
    protected function storeUploads(Request $request, string $field, string $kind, string $folder): array
    {
        $paths = [];

        foreach ($request->file($field) ?? [] as $file) {
            $paths[] = $kind === 'file'
                ? Media::storeFile($file, $folder)
                : Media::storeImage($file, $folder);
        }

        return $paths;
    }

    /**
     * @param  list<string>  $paths
     */
    protected function deleteUploads(iterable $paths, string $disk): void
    {
        $storage = Storage::disk($disk);

        foreach ($paths as $path) {
            if (filled($path)) {
                $storage->delete($path);
            }
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
            'images' => $this->imageUrls($item),
            'video' => $item->video ? Media::files()->url($item->video) : null,
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
            'images' => collect($item->galleryImages())
                ->map(fn (string $path) => [
                    'path' => $path,
                    'url' => Media::images()->url($path),
                ])
                ->values()
                ->all(),
            'video' => $item->video
                ? ['path' => $item->video, 'url' => Media::files()->url($item->video)]
                : null,
            'description' => $item->description,
            'sort_order' => $item->sort_order,
            'is_active' => $item->is_active,
        ]);
    }

    /**
     * @return list<string>
     */
    protected function imageUrls(CampusEvent $item): array
    {
        return array_map(
            fn (string $path): string => Media::images()->url($path),
            $item->galleryImages(),
        );
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $item = CampusEvent::findOrFail($id);

        $this->normalizeSlugInput($request);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('campus_events', 'slug')->ignore($item->id)],
            'date' => 'required|date',
            'images' => 'nullable|array|max:'.self::MAX_IMAGES,
            'images.*' => 'required|image|mimes:jpeg,jpg,png,webp|max:'.self::MAX_IMAGE_KILOBYTES,
            'video' => $this->videoRules(),
            'remove_images' => 'nullable|array',
            'remove_images.*' => 'string|max:255',
            'remove_video' => 'nullable|boolean',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'title.required' => 'শিরোনাম আবশ্যক।',
            'date.required' => 'তারিখ আবশ্যক।',
            'date.date' => 'সঠিক তারিখ দিন।',
            'images.array' => 'সর্বোচ্চ '.self::MAX_IMAGES.'টি ছবি যোগ করা যাবে।',
            'images.*.image' => 'সঠিক ছবি আপলোড করুন।',
            'images.*.mimes' => 'ছবির ফরম্যাট jpeg, jpg, png বা webp হতে হবে।',
            'images.*.max' => 'ছবির আকার ৫ এমবির বেশি হতে পারবে না।',
            'slug.unique' => 'এই ঠিকানাটি (slug) ইতিমধ্যে ব্যবহার করা হয়েছে।',
        ] + $this->videoMessages());

        $currentImages = $item->galleryImages();
        $removedImages = array_values(array_intersect(
            (array) $request->input('remove_images', []),
            $currentImages,
        ));
        $keptImages = array_values(array_diff($currentImages, $removedImages));
        $uploads = $request->file('images') ?? [];

        // The gallery is the one thing the form cannot leave empty, and the
        // cross buttons only say what to drop, so the final list has to be
        // counted here rather than by either rule.
        $total = count($keptImages) + count($uploads);

        if ($total < 1) {
            throw ValidationException::withMessages(['images' => 'কমপক্ষে একটি ছবি আবশ্যক।']);
        }

        if ($total > self::MAX_IMAGES) {
            throw ValidationException::withMessages([
                'images' => 'সর্বোচ্চ '.self::MAX_IMAGES.'টি ছবি রাখা যাবে।',
            ]);
        }

        $storedImages = [];
        $storedVideo = null;
        $previousVideo = $item->video;
        $videoPath = $previousVideo;

        try {
            $storedImages = $this->storeUploads($request, 'images', 'image', 'campus-events');

            if ($request->hasFile('video')) {
                $storedVideo = Media::storeFile($request->file('video'), 'campus-events');
                $videoPath = $storedVideo;
            } elseif ($request->boolean('remove_video')) {
                $videoPath = null;
            }

            $gallery = array_values(array_merge($keptImages, $storedImages));

            $payload = $validated;
            unset($payload['images'], $payload['video'], $payload['remove_images'], $payload['remove_video']);

            // A blank field leaves the published address alone rather than
            // wiping a link that has already been shared.
            if (blank($payload['slug'] ?? null)) {
                unset($payload['slug']);
            }

            $payload['image'] = $gallery[0];
            $payload['images'] = $gallery;
            $payload['video'] = $videoPath;

            $item->update($payload);
        } catch (\Exception $e) {
            $this->deleteUploads($storedImages, 'public');
            $this->deleteUploads(array_filter([$storedVideo]), 'public_files');

            return back()->withInput()
                ->with('error', 'ক্যাম্পাস ইভেন্ট আপডেট করতে সমস্যা হয়েছে। '.$e->getMessage());
        }

        // Only files the saved record no longer points at are dropped, so a
        // failed save above never takes an image the page still uses.
        $this->deleteUploads(array_diff($currentImages, $gallery), 'public');

        if ($previousVideo && $previousVideo !== $videoPath) {
            $this->deleteUploads([$previousVideo], 'public_files');
        }

        return redirect()->route('admin.campus-events.index')
            ->with('success', 'ক্যাম্পাস ইভেন্ট সফলভাবে আপডেট হয়েছে।');
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
            $item = CampusEvent::findOrFail($id);

            // The item is not restorable from the admin trash, so its uploads
            // can go the moment the record does rather than being orphaned in
            // the public folder forever.
            $this->deleteUploads($item->galleryImages(), 'public');
            $this->deleteUploads([$item->video], 'public_files');

            $item->delete();

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
