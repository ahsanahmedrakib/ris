<?php

namespace App\Features\Website\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\ContactMessage;
use App\Models\Notice;
use App\Models\Staff;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebsiteController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_students' => Student::where('is_active', true)->count(),
            'total_teachers' => User::where('role', 'teacher')->where('is_active', true)->count(),
            'total_classes' => ClassRoom::count(),
            'total_staff' => Staff::count(),
        ];

        $notices = Notice::where('is_active', true)
            ->where('published_at', '<=', now())
            ->orderByDesc('published_at')
            ->take(5)
            ->get();

        return view('website.index', compact('stats', 'notices'));
    }

    public function about(): View
    {
        return view('website.about');
    }

    public function admission(): View
    {
        $classes = ClassRoom::orderBy('name')->get();

        return view('website.admission', compact('classes'));
    }

    public function scholarship(): View
    {
        return view('website.scholarship');
    }

    public function contact(): View
    {
        return view('website.contact');
    }

    public function sendContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ], [
            'name.required' => 'নাম আবশ্যক।',
            'name.max' => 'নাম ২৫৫ অক্ষরের বেশি হতে পারবে না।',
            'email.required' => 'ইমেইল আবশ্যক।',
            'email.email' => 'সঠিক ইমেইল দিন।',
            'email.max' => 'ইমেইল ২৫৫ অক্ষরের বেশি হতে পারবে না।',
            'phone.max' => 'ফোন নম্বর ২০ অক্ষরের বেশি হতে পারবে না।',
            'subject.required' => 'বিষয় আবশ্যক।',
            'subject.max' => 'বিষয় ২৫৫ অক্ষরের বেশি হতে পারবে না।',
            'message.required' => 'বার্তা আবশ্যক।',
        ]);

        ContactMessage::create($validated);

        return redirect()->route('contact')->with('success', 'আপনার বার্তা সফলভাবে পাঠানো হয়েছে। আমরা শীঘ্রই যোগাযোগ করব।');
    }

    public function notices(): View
    {
        $notices = Notice::where('is_active', true)
            ->where('published_at', '<=', now())
            ->orderByDesc('published_at')
            ->paginate(10);

        return view('website.notices', compact('notices'));
    }
}
