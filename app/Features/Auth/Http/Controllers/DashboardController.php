<?php

namespace App\Features\Auth\Http\Controllers;

use App\Enums\FeeStatus;
use App\Http\Controllers\Controller;
use App\Models\FeeInvoice;
use App\Models\FeePayment;
use App\Models\Notice;
use App\Models\Staff;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function adminDashboard(Request $request): View
    {
        $stats = [
            'total_students' => Student::where('is_active', true)->count(),
            'total_teachers' => User::where('role', 'teacher')->where('is_active', true)->count(),
            'total_staff' => Staff::count(),
            'total_notices' => Notice::where('is_active', true)->count(),
            'fees_collected' => FeePayment::sum('amount'),
            'fees_pending' => FeeInvoice::whereIn('status', [FeeStatus::Pending, FeeStatus::Partial, FeeStatus::Overdue])->sum('amount'),
            'pending_admissions' => Student::where('is_active', false)->count(),
            'recent_notices' => Notice::orderByDesc('published_at')->take(5)->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    public function parentDashboard(Request $request): View
    {
        $user = $request->user();
        $children = $user->parentStudents()->with('classRoom')->get();

        $stats = [
            'total_children' => $children->count(),
            'total_notices' => Notice::where('is_active', true)->count(),
        ];

        $notices = Notice::where('is_active', true)
            ->where('published_at', '<=', now())
            ->orderByDesc('published_at')
            ->take(5)
            ->get();

        return view('parent.dashboard', compact('children', 'stats', 'notices'));
    }
}
