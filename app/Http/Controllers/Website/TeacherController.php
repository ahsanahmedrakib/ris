<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public function index(): View
    {
        $teachers = User::where('role', 'teacher')
            ->where('is_active', true)
            ->with('teacherProfile')
            ->whereHas('teacherProfile')
            ->orderByRaw("(SELECT FIELD(designation, 'প্রধান শিক্ষক', 'সহকারী প্রধান শিক্ষক', 'সহকারী শিক্ষক', 'শিক্ষক') FROM teacher_profiles WHERE user_id = users.id LIMIT 1)")
            ->get();

        return view('website.teachers.index', compact('teachers'));
    }

    public function single(string $slug): View
    {
        $teacher = User::where('role', 'teacher')
            ->where('is_active', true)
            ->whereHas('teacherProfile', function ($q) use ($slug) {
                $q->where('slug', $slug)->where('is_active', true);
            })
            ->with('teacherProfile')
            ->firstOrFail();

        return view('website.teachers.single', compact('teacher'));
    }
}
