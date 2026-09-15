<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\TeacherProfile;
use App\Models\User;
use App\Support\XlsxExport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public const DESIGNATIONS = [
        'প্রধান শিক্ষক' => 'প্রধান শিক্ষক',
        'সহকারী প্রধান শিক্ষক' => 'সহকারী প্রধান শিক্ষক',
        'সহকারী শিক্ষক' => 'সহকারী শিক্ষক',
        'শিক্ষক' => 'শিক্ষক',
    ];

    public function index(Request $request): View
    {
        $query = User::where('role', 'teacher')->with('teacherProfile');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhereHas('teacherProfile', function ($q2) use ($search) {
                        $q2->where('subject', 'like', "%{$search}%")
                            ->orWhere('designation', 'like', "%{$search}%");
                    });
            });
        }

        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $teachers = $query->orderByRaw("(SELECT FIELD(designation, 'প্রধান শিক্ষক', 'সহকারী প্রধান শিক্ষক', 'সহকারী শিক্ষক', 'শিক্ষক') FROM teacher_profiles WHERE user_id = users.id LIMIT 1)")
            ->paginate($perPage)->withQueryString();

        return view('admin.teachers.index', [
            'teachers' => $teachers,
            'designations' => self::DESIGNATIONS,
            'breadcrumbs' => ['শিক্ষক' => null],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'photo' => 'required|image|max:2048',
            'designation' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'qualification' => 'required|string|max:255',
            'institute' => 'required|string|max:255',
            'joining_date' => 'required|date',
            'previous_institutions' => 'nullable|string',
            'bio' => 'nullable|string',
            'experience' => 'nullable|string',
            'achievements' => 'nullable|string',
        ], [
            'name.required' => 'নাম আবশ্যক।',
            'email.required' => 'ইমেইল আবশ্যক।',
            'email.email' => 'সঠিক ইমেইল দিন।',
            'email.unique' => 'এই ইমেইল ইতিমধ্যে ব্যবহৃত হয়েছে।',
            'phone.required' => 'ফোন নম্বর আবশ্যক।',
            'photo.required' => 'ছবি আবশ্যক।',
            'photo.image' => 'ছবি ফাইল হতে হবে।',
            'photo.max' => 'ছবির সাইজ ২MB এর কম হতে হবে।',
            'designation.required' => 'পদবি আবশ্যক।',
            'subject.required' => 'বিষয় আবশ্যক।',
            'qualification.required' => 'যোগ্যতা আবশ্যক।',
            'institute.required' => 'প্রতিষ্ঠান আবশ্যক।',
            'joining_date.required' => 'যোগদানের তারিখ আবশ্যক।',
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => bcrypt('password'),
                'role' => UserRole::Teacher->value,
                'is_active' => true,
            ]);

            $photoPath = $request->file('photo')->store('teachers', 'public');

            TeacherProfile::create([
                'user_id' => $user->id,
                'photo' => $photoPath,
                'designation' => $validated['designation'],
                'subject' => $validated['subject'],
                'qualification' => $validated['qualification'],
                'institute' => $validated['institute'],
                'previous_institutions' => $validated['previous_institutions'] ?? null,
                'joining_date' => $validated['joining_date'],
                'bio' => $validated['bio'] ?? null,
                'experience' => $validated['experience'] ?? null,
                'achievements' => $validated['achievements'] ?? null,
            ]);

            DB::commit();

            return redirect()->route('admin.teachers.index')
                ->with('success', 'শিক্ষক সফলভাবে যোগ করা হয়েছে।');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'শিক্ষক যোগ করতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }

    public function show(User $teacher): JsonResponse
    {
        $teacher->load('teacherProfile');

        return response()->json([
            'id' => $teacher->id,
            'name' => $teacher->name,
            'email' => $teacher->email,
            'phone' => $teacher->phone,
            'is_active' => $teacher->is_active,
            'photo' => $teacher->teacherProfile?->photo ? Storage::url($teacher->teacherProfile->photo) : null,
            'designation' => $teacher->teacherProfile?->designation,
            'subject' => $teacher->teacherProfile?->subject,
            'qualification' => $teacher->teacherProfile?->qualification,
            'institute' => $teacher->teacherProfile?->institute,
            'previous_institutions' => $teacher->teacherProfile?->previous_institutions,
            'joining_date' => $teacher->teacherProfile?->joining_date?->format('d/m/Y'),
            'bio' => $teacher->teacherProfile?->bio,
            'experience' => $teacher->teacherProfile?->experience,
            'achievements' => $teacher->teacherProfile?->achievements,
            'slug' => $teacher->teacherProfile?->slug,
            'created_at' => $teacher->created_at->format('d/m/Y h:i A'),
        ]);
    }

    public function edit(User $teacher): JsonResponse
    {
        $teacher->load('teacherProfile');

        return response()->json([
            'id' => $teacher->id,
            'name' => $teacher->name,
            'email' => $teacher->email,
            'phone' => $teacher->phone,
            'is_active' => $teacher->is_active,
            'photo' => $teacher->teacherProfile?->photo ? Storage::url($teacher->teacherProfile->photo) : null,
            'designation' => $teacher->teacherProfile?->designation ?? '',
            'subject' => $teacher->teacherProfile?->subject ?? '',
            'qualification' => $teacher->teacherProfile?->qualification ?? '',
            'institute' => $teacher->teacherProfile?->institute ?? '',
            'previous_institutions' => $teacher->teacherProfile?->previous_institutions ?? '',
            'joining_date' => $teacher->teacherProfile?->joining_date?->format('d/m/Y') ?? '',
            'bio' => $teacher->teacherProfile?->bio ?? '',
            'experience' => $teacher->teacherProfile?->experience ?? '',
            'achievements' => $teacher->teacherProfile?->achievements ?? '',
        ]);
    }

    public function update(Request $request, User $teacher): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$teacher->id}",
            'phone' => 'required|string|max:20',
            'photo' => 'nullable|image|max:2048',
            'designation' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'qualification' => 'required|string|max:255',
            'institute' => 'required|string|max:255',
            'joining_date' => 'required|date',
            'previous_institutions' => 'nullable|string',
            'bio' => 'nullable|string',
            'experience' => 'nullable|string',
            'achievements' => 'nullable|string',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'নাম আবশ্যক।',
            'email.required' => 'ইমেইল আবশ্যক।',
            'email.email' => 'সঠিক ইমেইল দিন।',
            'email.unique' => 'এই ইমেইল ইতিমধ্যে ব্যবহৃত হয়েছে।',
            'phone.required' => 'ফোন নম্বর আবশ্যক।',
            'photo.image' => 'ছবি ফাইল হতে হবে।',
            'photo.max' => 'ছবির সাইজ ২MB এর কম হতে হবে।',
            'designation.required' => 'পদবি আবশ্যক।',
            'subject.required' => 'বিষয় আবশ্যক।',
            'qualification.required' => 'যোগ্যতা আবশ্যক।',
            'institute.required' => 'প্রতিষ্ঠান আবশ্যক।',
            'joining_date.required' => 'যোগদানের তারিখ আবশ্যক।',
        ]);

        DB::beginTransaction();

        try {
            $teacher->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            $photoPath = $teacher->teacherProfile?->photo;
            if ($request->hasFile('photo')) {
                if ($photoPath) {
                    Storage::disk('public')->delete($photoPath);
                }
                $photoPath = $request->file('photo')->store('teachers', 'public');
            }

            $profileData = [
                'photo' => $photoPath,
                'designation' => $validated['designation'],
                'subject' => $validated['subject'],
                'qualification' => $validated['qualification'],
                'institute' => $validated['institute'],
                'previous_institutions' => $validated['previous_institutions'] ?? null,
                'joining_date' => $validated['joining_date'],
                'bio' => $validated['bio'] ?? null,
                'experience' => $validated['experience'] ?? null,
                'achievements' => $validated['achievements'] ?? null,
            ];

            if ($teacher->teacherProfile) {
                $teacher->teacherProfile()->update($profileData);
            } else {
                $profileData['user_id'] = $teacher->id;
                TeacherProfile::create($profileData);
            }

            DB::commit();

            return redirect()->route('admin.teachers.index')
                ->with('success', 'শিক্ষকের তথ্য সফলভাবে আপডেট হয়েছে।');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'শিক্ষক আপডেট করতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }

    public function destroy(User $teacher): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $teacher->teacherProfile?->delete();
            $teacher->delete();

            DB::commit();

            return redirect()->route('admin.teachers.index')
                ->with('success', 'শিক্ষক সফলভাবে ট্র্যাশে পাঠানো হয়েছে।');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->with('error', 'শিক্ষক মুছে ফেলতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }

    public function download(Request $request)
    {
        $query = User::where('role', 'teacher')->with('teacherProfile');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhereHas('teacherProfile', function ($q2) use ($search) {
                        $q2->where('subject', 'like', "%{$search}%")
                            ->orWhere('designation', 'like', "%{$search}%");
                    });
            });
        }

        $teachers = $query->orderByRaw("(SELECT FIELD(designation, 'প্রধান শিক্ষক', 'সহকারী প্রধান শিক্ষক', 'সহকারী শিক্ষক', 'শিক্ষক') FROM teacher_profiles WHERE user_id = users.id LIMIT 1)")->get();

        $rows = $teachers->map(fn ($teacher, $index) => [
            $index + 1,
            $teacher->name,
            $teacher->email,
            $teacher->phone ?? '-',
            $teacher->teacherProfile?->designation ?? '-',
            $teacher->teacherProfile?->subject ?? '-',
            $teacher->teacherProfile?->qualification ?? '-',
            $teacher->teacherProfile?->institute ?? '-',
            $teacher->teacherProfile?->joining_date?->format('d/m/Y') ?? '-',
            $teacher->is_active ? 'সক্রিয়' : 'ডিলিট',
        ])->all();

        return XlsxExport::download(
            ['ক্রমিক', 'নাম', 'ইমেইল', 'ফোন', 'পদবি', 'বিষয়', 'যোগ্যতা', 'প্রতিষ্ঠান', 'যোগদান', 'স্ট্যাটাস'],
            $rows,
            'teachers_'.now('Asia/Dhaka')->format('Y-m-d_H-i').'.xlsx',
        );
    }

    public function toggleActive(int $id): JsonResponse
    {
        $teacher = User::with('teacherProfile')->findOrFail($id);
        $teacher->is_active = ! $teacher->is_active;
        $teacher->save();

        if ($teacher->teacherProfile) {
            $teacher->teacherProfile->is_active = $teacher->is_active;
            $teacher->teacherProfile->save();
        }

        return response()->json([
            'id' => $teacher->id,
            'is_active' => $teacher->is_active,
            'status_label' => $teacher->is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়',
        ]);
    }
}
