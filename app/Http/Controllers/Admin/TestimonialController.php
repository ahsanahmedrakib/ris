<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TestimonialController extends Controller
{
    public function index(Request $request): View
    {
        $query = Testimonial::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('designation', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $testimonials = $query->orderBy('sort_order')->orderByDesc('id')->paginate($perPage)->withQueryString();

        return view('admin.testimonials.index', ['testimonials' => $testimonials]);
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('admin.testimonials.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'designation' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'message' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'নাম আবশ্যক।',
            'message.required' => 'মন্তব্য আবশ্যক।',
            'rating.required' => 'রেটিং আবশ্যক।',
            'rating.min' => 'রেটিং ১ থেকে ৫ এর মধ্যে হতে হবে।',
            'rating.max' => 'রেটিং ১ থেকে ৫ এর মধ্যে হতে হবে।',
            'photo.image' => 'সঠিক ছবি আপলোড করুন।',
            'photo.mimes' => 'ছবির ফরম্যাট jpeg, jpg, png বা webp হতে হবে।',
            'photo.max' => 'ছবির আকার ২ এমবির বেশি হতে পারবে না।',
        ]);

        try {
            $photoPath = null;

            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('testimonial-photos', 'public');
            }

            Testimonial::create([
                'name' => $validated['name'],
                'designation' => $validated['designation'] ?? null,
                'photo' => $photoPath,
                'message' => $validated['message'],
                'rating' => $validated['rating'],
                'sort_order' => $validated['sort_order'] ?? 0,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            return redirect()->route('admin.testimonials.index')
                ->with('success', 'টেস্টিমোনিয়াল সফলভাবে যোগ করা হয়েছে।');
        } catch (\Exception $e) {
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }

            return back()->withInput()
                ->with('error', 'টেস্টিমোনিয়াল যোগ করতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        $testimonial = Testimonial::findOrFail($id);

        return response()->json([
            'id' => $testimonial->id,
            'name' => $testimonial->name,
            'designation' => $testimonial->designation,
            'photo' => $testimonial->photo ? Storage::url($testimonial->photo) : null,
            'message' => $testimonial->message,
            'rating' => $testimonial->rating,
            'is_active' => $testimonial->is_active,
            'status_label' => $testimonial->is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়',
            'created_at' => $testimonial->created_at->format('d/m/Y h:i A'),
        ]);
    }

    public function edit(int $id): JsonResponse
    {
        $testimonial = Testimonial::findOrFail($id);

        return response()->json([
            'id' => $testimonial->id,
            'name' => $testimonial->name,
            'designation' => $testimonial->designation,
            'photo' => $testimonial->photo ? Storage::url($testimonial->photo) : null,
            'message' => $testimonial->message,
            'rating' => (string) $testimonial->rating,
            'sort_order' => (string) $testimonial->sort_order,
            'is_active' => $testimonial->is_active,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $testimonial = Testimonial::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'designation' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'message' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'নাম আবশ্যক।',
            'message.required' => 'মন্তব্য আবশ্যক।',
            'rating.required' => 'রেটিং আবশ্যক।',
            'rating.min' => 'রেটিং ১ থেকে ৫ এর মধ্যে হতে হবে।',
            'rating.max' => 'রেটিং ১ থেকে ৫ এর মধ্যে হতে হবে।',
            'photo.image' => 'সঠিক ছবি আপলোড করুন।',
            'photo.mimes' => 'ছবির ফরম্যাট jpeg, jpg, png বা webp হতে হবে।',
            'photo.max' => 'ছবির আকার ২ এমবির বেশি হতে পারবে না।',
        ]);

        try {
            if ($request->hasFile('photo')) {
                if ($testimonial->photo) {
                    Storage::disk('public')->delete($testimonial->photo);
                }

                $validated['photo'] = $request->file('photo')->store('testimonial-photos', 'public');
            }

            $validated['is_active'] = $validated['is_active'] ?? $testimonial->is_active;
            $testimonial->update($validated);

            return redirect()->route('admin.testimonials.index')
                ->with('success', 'টেস্টিমোনিয়াল সফলভাবে আপডেট হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'টেস্টিমোনিয়াল আপডেট করতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }

    public function toggleActive(int $id): JsonResponse
    {
        $testimonial = Testimonial::findOrFail($id);
        $testimonial->is_active = ! $testimonial->is_active;
        $testimonial->save();

        return response()->json([
            'id' => $testimonial->id,
            'is_active' => $testimonial->is_active,
            'status_label' => $testimonial->is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়',
        ]);
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            Testimonial::findOrFail($id)->delete();

            return redirect()->route('admin.testimonials.index')
                ->with('success', 'টেস্টিমোনিয়াল সফলভাবে মুছে ফেলা হয়েছে।');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'টেস্টিমোনিয়াল মুছে ফেলতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }
}
