<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(Request $request): View
    {
        $query = Message::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('designation', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $messages = $query->orderBy('sort_order')->orderBy('id')->paginate($perPage)->withQueryString();

        return view('admin.messages.index', ['messages' => $messages]);
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('admin.messages.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'designation' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'message' => 'required|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'title.required' => 'শিরোনাম আবশ্যক।',
            'name.required' => 'নাম আবশ্যক।',
            'message.required' => 'বার্তা আবশ্যক।',
            'photo.image' => 'সঠিক ছবি আপলোড করুন।',
            'photo.mimes' => 'ছবির ফরম্যাট jpeg, jpg, png বা webp হতে হবে।',
            'photo.max' => 'ছবির আকার ২ এমবির বেশি হতে পারবে না।',
        ]);

        try {
            $photoPath = null;

            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('messages', 'public');
            }

            Message::create([
                'title' => $validated['title'],
                'name' => $validated['name'],
                'designation' => $validated['designation'] ?? null,
                'photo' => $photoPath,
                'message' => $validated['message'],
                'sort_order' => $validated['sort_order'] ?? 0,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            return redirect()->route('admin.messages.index')
                ->with('success', 'বার্তা সফলভাবে যোগ করা হয়েছে।');
        } catch (\Exception $e) {
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }

            return back()->withInput()
                ->with('error', 'বার্তা যোগ করতে সমস্যা হয়েছে. '.$e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        $message = Message::findOrFail($id);

        return response()->json([
            'id' => $message->id,
            'title' => $message->title,
            'name' => $message->name,
            'designation' => $message->designation,
            'photo' => $message->photo ? Storage::url($message->photo) : null,
            'message' => $message->message,
            'is_active' => $message->is_active,
            'status_label' => $message->is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়',
            'created_at' => $message->created_at->format('d/m/Y h:i A'),
        ]);
    }

    public function edit(int $id): JsonResponse
    {
        $message = Message::findOrFail($id);

        return response()->json([
            'id' => $message->id,
            'title' => $message->title,
            'name' => $message->name,
            'designation' => $message->designation,
            'photo' => $message->photo ? Storage::url($message->photo) : null,
            'message' => $message->message,
            'sort_order' => (string) $message->sort_order,
            'is_active' => $message->is_active,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $message = Message::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'designation' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'message' => 'required|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'title.required' => 'শিরোনাম আবশ্যক।',
            'name.required' => 'নাম আবশ্যক।',
            'message.required' => 'বার্তা আবশ্যক।',
            'photo.image' => 'সঠিক ছবি আপলোড করুন।',
            'photo.mimes' => 'ছবির ফরম্যাট jpeg, jpg, png বা webp হতে হবে।',
            'photo.max' => 'ছবির আকার ২ এমবির বেশি হতে পারবে না।',
        ]);

        try {
            if ($request->hasFile('photo')) {
                if ($message->photo) {
                    Storage::disk('public')->delete($message->photo);
                }

                $validated['photo'] = $request->file('photo')->store('messages', 'public');
            }

            $validated['is_active'] = $validated['is_active'] ?? $message->is_active;
            $message->update($validated);

            return redirect()->route('admin.messages.index')
                ->with('success', 'বার্তা সফলভাবে আপডেট হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'বার্তা আপডেট করতে সমস্যা হয়েছে. '.$e->getMessage());
        }
    }

    public function toggleActive(int $id): JsonResponse
    {
        $message = Message::findOrFail($id);
        $message->is_active = ! $message->is_active;
        $message->save();

        return response()->json([
            'id' => $message->id,
            'is_active' => $message->is_active,
            'status_label' => $message->is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়',
        ]);
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            Message::findOrFail($id)->delete();

            return redirect()->route('admin.messages.index')
                ->with('success', 'বার্তা সফলভাবে মুছে ফেলা হয়েছে।');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'বার্তা মুছে ফেলতে সমস্যা হয়েছে. '.$e->getMessage());
        }
    }
}
