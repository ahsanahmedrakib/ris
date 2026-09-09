<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::where('role', 'teacher');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $teachers = $query->latest()->paginate(15)->withQueryString();

        return view('admin.teachers.index', compact('teachers'));
    }

    public function create(): View
    {
        return view('admin.teachers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
        ], [
            'name.required' => 'নাম আবশ্যক।',
            'email.required' => 'ইমেইল আবশ্যক।',
            'email.email' => 'সঠিক ইমেইল দিন।',
            'email.unique' => 'এই ইমেইল ইতিমধ্যে ব্যবহৃত হয়েছে।',
        ]);

        try {
            User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'password' => bcrypt('password'),
                'role' => UserRole::Teacher->value,
                'is_active' => true,
            ]);

            return redirect()->route('admin.teachers.index')
                ->with('success', 'শিক্ষক সফলভাবে যোগ করা হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'শিক্ষক যোগ করতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }

    public function show(int $id): View
    {
        $teacher = User::where('role', 'teacher')->findOrFail($id);

        return view('admin.teachers.show', compact('teacher'));
    }

    public function edit(int $id): View
    {
        $teacher = User::where('role', 'teacher')->findOrFail($id);

        return view('admin.teachers.edit', compact('teacher'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $teacher = User::where('role', 'teacher')->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$teacher->id}",
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'নাম আবশ্যক।',
            'email.required' => 'ইমেইল আবশ্যক।',
            'email.email' => 'সঠিক ইমেইল দিন।',
            'email.unique' => 'এই ইমেইল ইতিমধ্যে ব্যবহৃত হয়েছে।',
        ]);

        try {
            $teacher->update($validated);

            return redirect()->route('admin.teachers.index')
                ->with('success', 'শিক্ষকের তথ্য সফলভাবে আপডেট হয়েছে।');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'শিক্ষক আপডেট করতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $teacher = User::where('role', 'teacher')->findOrFail($id);
            $teacher->update(['is_active' => false]);

            return redirect()->route('admin.teachers.index')
                ->with('success', 'শিক্ষক সফলভাবে মুছে ফেলা হয়েছে।');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'শিক্ষক মুছে ফেলতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }
}
