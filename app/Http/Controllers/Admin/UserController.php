<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::where('role', UserRole::Admin->value);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:50|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ], [
            'name.required' => 'নাম আবশ্যক।',
            'name.string' => 'নাম অবশ্যই একটি স্ট্রিং হতে হবে।',
            'email.required' => 'ইমেইল আবশ্যক।',
            'email.email' => 'সঠিক ইমেইল দিন।',
            'email.unique' => 'এই ইমেইল ইতিমধ্যে ব্যবহৃত হয়েছে।',
            'username.unique' => 'এই ইউজারনেম ইতিমধ্যে ব্যবহৃত হয়েছে।',
            'password.required' => 'পাসওয়ার্ড আবশ্যক।',
            'password.min' => 'পাসওয়ার্ড কমপক্ষে ৮ অক্ষরের হতে হবে।',
            'password.confirmed' => 'পাসওয়ার্ড দুটি মিলে যায়নি।',
            'avatar.image' => 'সঠিক ছবি আপলোড করুন।',
            'avatar.mimes' => 'ছবির ফরম্যাট jpeg, jpg, png বা webp হতে হবে।',
            'avatar.max' => 'ছবির আকার ২ এমবির বেশি হতে পারবে না।',
        ]);

        $avatarPath = null;

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        User::create([
            'name' => $validated['name'],
            'username' => $validated['username'] ?? null,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => $validated['password'],
            'role' => UserRole::Admin->value,
            'is_active' => true,
            'avatar' => $avatarPath,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'অ্যাডমিন সফলভাবে যোগ করা হয়েছে।');
    }

    public function show(int $id): JsonResponse
    {
        $user = User::where('role', UserRole::Admin->value)->findOrFail($id);

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username ?? '',
            'email' => $user->email,
            'phone' => $user->phone ?? '',
            'avatar' => $user->avatarUrl,
            'is_active' => (bool) $user->is_active,
            'created_at' => $user->created_at->format('d/m/Y h:i A'),
        ]);
    }

    public function edit(int $id): JsonResponse
    {
        $user = User::where('role', UserRole::Admin->value)->findOrFail($id);

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username ?? '',
            'email' => $user->email,
            'phone' => $user->phone ?? '',
            'avatar' => $user->avatarUrl,
            'is_active' => (bool) $user->is_active,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $user = User::where('role', UserRole::Admin->value)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => "nullable|string|max:50|unique:users,username,{$user->id}",
            'email' => "required|email|unique:users,email,{$user->id}",
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ], [
            'name.required' => 'নাম আবশ্যক।',
            'email.required' => 'ইমেইল আবশ্যক।',
            'email.email' => 'সঠিক ইমেইল দিন।',
            'email.unique' => 'এই ইমেইল ইতিমধ্যে ব্যবহৃত হয়েছে।',
            'username.unique' => 'এই ইউজারনেম ইতিমধ্যে ব্যবহৃত হয়েছে।',
            'password.min' => 'পাসওয়ার্ড কমপক্ষে ৮ অক্ষরের হতে হবে।',
            'password.confirmed' => 'পাসওয়ার্ড দুটি মিলে যায়নি।',
            'avatar.image' => 'সঠিক ছবি আপলোড করুন।',
            'avatar.mimes' => 'ছবির ফরম্যাট jpeg, jpg, png বা webp হতে হবে।',
            'avatar.max' => 'ছবির আকার ২ এমবির বেশি হতে পারবে না।',
        ]);

        $data = [
            'name' => $validated['name'],
            'username' => $validated['username'] ?? null,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ];

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        if ($request->boolean('remove_avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = null;
        }

        if (! empty($validated['password'])) {
            $data['password'] = $validated['password'];
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'অ্যাডমিনের তথ্য সফলভাবে আপডেট হয়েছে।');
    }

    public function destroy(int $id): RedirectResponse
    {
        if ($id === Auth::id()) {
            return back()->with('error', 'নিজের অ্যাকাউন্ট মুছে ফেলা যাবে না।');
        }

        $user = User::where('role', UserRole::Admin->value)->findOrFail($id);
        $user->update(['is_active' => false]);
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'অ্যাডমিন সফলভাবে মুছে ফেলা হয়েছে।');
    }

    public function toggleActive(int $id): JsonResponse
    {
        if ($id === Auth::id()) {
            return response()->json(['message' => 'নিজের অ্যাকাউন্ট নিষ্ক্রিয় করা যাবে না।'], 422);
        }

        $user = User::where('role', UserRole::Admin->value)->findOrFail($id);
        $user->is_active = ! $user->is_active;
        $user->save();

        return response()->json([
            'id' => $user->id,
            'is_active' => (bool) $user->is_active,
            'status_label' => $user->is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়',
        ]);
    }
}
