<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScholarshipRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScholarshipController extends Controller
{
    public function index(Request $request): View
    {
        $query = ScholarshipRegistration::with('creator');

        if ($request->filled('class_no')) {
            $query->where('class_no', $request->class_no);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('registration_no', 'like', "%{$search}%")
                    ->orWhere('student_name', 'like', "%{$search}%")
                    ->orWhere('father_name', 'like', "%{$search}%")
                    ->orWhere('mobile_no', 'like', "%{$search}%");
            });
        }

        $registrations = $query->latest()->paginate(15)->withQueryString();

        return view('admin.scholarship.index', [
            'registrations' => $registrations,
            'classes' => ScholarshipRegistration::CLASSES,
            'breadcrumbs' => ['মেধাবৃত্তি' => null],
        ]);
    }

    public function create(): View
    {
        return view('admin.scholarship.create', [
            'breadcrumbs' => [
                'মেধাবৃত্তি' => route('admin.scholarship.index'),
                'নতুন রেজিস্ট্রেশন' => null,
            ],
        ]);
    }

    public function show(ScholarshipRegistration $scholarshipRegistration): View
    {
        $scholarshipRegistration->load('creator');

        return view('admin.scholarship.show', [
            'registration' => $scholarshipRegistration,
            'classes' => ScholarshipRegistration::CLASSES,
            'breadcrumbs' => [
                'মেধাবৃত্তি' => route('admin.scholarship.index'),
                'রেজিস্ট্রেশন' => null,
            ],
        ]);
    }

    public function print(ScholarshipRegistration $scholarshipRegistration): View
    {
        return view('admin.scholarship.pdf', [
            'registration' => $scholarshipRegistration,
            'classes' => ScholarshipRegistration::CLASSES,
        ]);
    }

    public function destroy(ScholarshipRegistration $scholarshipRegistration): RedirectResponse
    {
        try {
            $registrationNo = $scholarshipRegistration->registration_no;
            $scholarshipRegistration->delete();

            return redirect()->route('admin.scholarship.index')
                ->with('success', 'মেধাবৃত্তি রেজিস্ট্রেশন ('.$registrationNo.') মুছে ফেলা হয়েছে।');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'রেজিস্ট্রেশন মুছে ফেলতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }
}
