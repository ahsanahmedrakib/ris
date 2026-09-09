<?php

namespace App\Features\Website\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
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
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // In production, send email or store in database
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
