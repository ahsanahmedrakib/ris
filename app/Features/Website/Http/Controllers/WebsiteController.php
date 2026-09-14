<?php

namespace App\Features\Website\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\ClassRoom;
use App\Models\ContactMessage;
use App\Models\Notice;
use App\Models\Staff;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

        $month = (int) now()->format('n');
        $year = $month >= 3 ? now()->addYear()->year : now()->year;
        $defaultAcademicYear = strtr((string) ($year % 100), [
            '0' => '০',
            '1' => '১',
            '2' => '২',
            '3' => '৩',
            '4' => '৪',
            '5' => '৫',
            '6' => '৬',
            '7' => '৭',
            '8' => '৮',
            '9' => '৯',
        ]);

        return view('website.admission', compact('classes', 'defaultAcademicYear'));
    }

    public function storeAdmission(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'academic_year' => 'required|string|max:10',
            'roll_no' => 'nullable|string|max:20',
            'section' => 'nullable|string|max:50',
            'batch' => 'nullable|array',
            'batch.*' => 'string|max:50',
            'admission_date' => 'nullable|date',
            'form_collect_date' => 'nullable|date',
            'form_submit_date' => 'nullable|date',
            'class_level' => 'nullable|array',
            'class_level.*' => 'string|max:50',
            'student_name_bn' => 'required|string|max:255',
            'student_name_en' => 'required|string|max:255',
            'dob' => 'required|date',
            'age' => 'required|string|max:20',
            'nationality' => 'required|string|max:255',
            'religion' => 'required|string|max:255',
            'blood_group' => 'required|string|max:20',
            'father_name_bn' => 'required|string|max:255',
            'father_name_en' => 'required|string|max:255',
            'father_occupation' => 'required|string|max:255',
            'mother_name_bn' => 'required|string|max:255',
            'mother_name_en' => 'required|string|max:255',
            'mother_occupation' => 'required|string|max:255',
            'present_address' => 'required|string',
            'permanent_address' => 'required|string',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'emergency_contact' => 'required|string|max:20',
            'legal_guardian_name' => 'required|string|max:255',
            'legal_guardian_occupation' => 'required|string|max:255',
            'legal_guardian_relation' => 'required|string|max:255',
            'legal_guardian_address' => 'required|string|max:255',
            'local_guardian_name' => 'required|string|max:255',
            'local_guardian_occupation' => 'required|string|max:255',
            'local_guardian_relation' => 'required|string|max:255',
            'local_guardian_address' => 'required|string|max:255',
            'local_guardian_phone' => 'required|string|max:20',
            'prev_school_name' => 'nullable|string|max:255',
            'prev_school_address' => 'nullable|string|max:255',
            'prev_roll_no' => 'nullable|string|max:20',
            'prev_marks' => 'nullable|string|max:50',
            'reference' => 'required|string|max:255',
            'reference_phone' => 'required|string|max:20',
            'reference_sign' => 'nullable|string|max:255',
            'student_photo' => 'required|image|mimes:jpeg,jpg,png|max:2048',
        ], [
            'academic_year.required' => 'শিক্ষাবর্ষ আবশ্যক।',
            'student_name_bn.required' => 'ছাত্র/ছাত্রীর নাম (বাংলায়) আবশ্যক।',
            'student_name_en.required' => 'ইংরেজিতে নাম আবশ্যক।',
            'dob.required' => 'জন্ম তারিখ আবশ্যক।',
            'age.required' => 'বয়স আবশ্যক।',
            'nationality.required' => 'জাতীয়তা আবশ্যক।',
            'religion.required' => 'ধর্ম আবশ্যক।',
            'blood_group.required' => 'ব্লাড গ্রুপ আবশ্যক।',
            'father_name_bn.required' => 'পিতার নাম (বাংলায়) আবশ্যক।',
            'father_name_en.required' => 'পিতার নাম (ইংরেজি) আবশ্যক।',
            'father_occupation.required' => 'পিতার পেশা আবশ্যক।',
            'mother_name_bn.required' => 'মাতার নাম (বাংলায়) আবশ্যক।',
            'mother_name_en.required' => 'মাতার নাম (ইংরেজি) আবশ্যক।',
            'mother_occupation.required' => 'মাতার পেশা আবশ্যক।',
            'present_address.required' => 'বর্তমান ঠিকানা আবশ্যক।',
            'permanent_address.required' => 'স্থায়ী ঠিকানা আবশ্যক।',
            'phone.required' => 'ফোন/মোবাইল আবশ্যক।',
            'emergency_contact.required' => 'জরুরি প্রয়োজনে ফোন আবশ্যক।',
            'legal_guardian_name.required' => 'আইনানুগ অভিভাবকের নাম আবশ্যক।',
            'legal_guardian_occupation.required' => 'আইনানুগ অভিভাবকের পেশা আবশ্যক।',
            'legal_guardian_relation.required' => 'আইনানুগ অভিভাবকের সম্পর্ক আবশ্যক।',
            'legal_guardian_address.required' => 'আইনানুগ অভিভাবকের ঠিকানা আবশ্যক।',
            'local_guardian_name.required' => 'স্থানীয় অভিভাবকের নাম আবশ্যক।',
            'local_guardian_occupation.required' => 'স্থানীয় অভিভাবকের পেশা আবশ্যক।',
            'local_guardian_relation.required' => 'স্থানীয় অভিভাবকের সম্পর্ক আবশ্যক।',
            'local_guardian_address.required' => 'স্থানীয় অভিভাবকের ঠিকানা আবশ্যক।',
            'local_guardian_phone.required' => 'স্থানীয় অভিভাবকের ফোন আবশ্যক।',
            'reference.required' => 'রেফারেন্স আবশ্যক।',
            'reference_phone.required' => 'রেফারেন্সের ফোন আবশ্যক।',
            'student_photo.required' => 'শিক্ষার্থীর ছবি আবশ্যক।',
            'email.email' => 'সঠিক ইমেইল দিন।',
            'student_photo.image' => 'সঠিক ছবি আপলোড করুন।',
            'student_photo.mimes' => 'ছবির ফরম্যাট jpeg, jpg বা png হতে হবে।',
            'student_photo.max' => 'ছবির আকার ২ এমবির বেশি হতে পারবে না।',
        ]);

        try {
            $photoPath = null;

            if ($request->hasFile('student_photo')) {
                $photoPath = $request->file('student_photo')->store('admissions', 'public');
            }

            $admission = Admission::create([
                'status' => 'pending',
                'academic_year' => $validated['academic_year'] ?? null,
                'roll_no' => $validated['roll_no'] ?? null,
                'section' => $validated['section'] ?? null,
                'batch' => $validated['batch'] ?? null,
                'admission_date' => $validated['admission_date'] ?? null,
                'form_collect_date' => $validated['form_collect_date'] ?? null,
                'form_submit_date' => $validated['form_submit_date'] ?? null,
                'class_level' => $validated['class_level'] ?? null,
                'student_name_bn' => $validated['student_name_bn'],
                'student_name_en' => $validated['student_name_en'],
                'dob' => $validated['dob'],
                'age' => $validated['age'] ?? null,
                'nationality' => $validated['nationality'] ?? null,
                'religion' => $validated['religion'] ?? null,
                'blood_group' => $validated['blood_group'] ?? null,
                'father_name_bn' => $validated['father_name_bn'],
                'father_name_en' => $validated['father_name_en'] ?? null,
                'father_occupation' => $validated['father_occupation'] ?? null,
                'mother_name_bn' => $validated['mother_name_bn'],
                'mother_name_en' => $validated['mother_name_en'] ?? null,
                'mother_occupation' => $validated['mother_occupation'] ?? null,
                'present_address' => $validated['present_address'] ?? null,
                'permanent_address' => $validated['permanent_address'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'] ?? null,
                'emergency_contact' => $validated['emergency_contact'] ?? null,
                'legal_guardian_name' => $validated['legal_guardian_name'] ?? null,
                'legal_guardian_occupation' => $validated['legal_guardian_occupation'] ?? null,
                'legal_guardian_relation' => $validated['legal_guardian_relation'] ?? null,
                'legal_guardian_address' => $validated['legal_guardian_address'] ?? null,
                'local_guardian_name' => $validated['local_guardian_name'] ?? null,
                'local_guardian_occupation' => $validated['local_guardian_occupation'] ?? null,
                'local_guardian_relation' => $validated['local_guardian_relation'] ?? null,
                'local_guardian_address' => $validated['local_guardian_address'] ?? null,
                'local_guardian_phone' => $validated['local_guardian_phone'] ?? null,
                'prev_school_name' => $validated['prev_school_name'] ?? null,
                'prev_school_address' => $validated['prev_school_address'] ?? null,
                'prev_roll_no' => $validated['prev_roll_no'] ?? null,
                'prev_marks' => $validated['prev_marks'] ?? null,
                'reference' => $validated['reference'] ?? null,
                'reference_phone' => $validated['reference_phone'] ?? null,
                'reference_sign' => $validated['reference_sign'] ?? null,
                'student_photo' => $photoPath,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'আপনার ভর্তি আবেদন সফলভাবে জমা হয়েছে। আমরা শীঘ্রই আপনার সাথে যোগাযোগ করব।',
                ]);
            }

            return redirect()->route('admission')
                ->with('success', 'আপনার ভর্তি আবেদন সফলভাবে জমা হয়েছে। আমরা শীঘ্রই আপনার সাথে যোগাযোগ করব।');
        } catch (\Exception $e) {
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'আবেদন জমা দিতে সমস্যা হয়েছে। আবার চেষ্টা করুন।',
                ], 422);
            }

            return back()->withInput()
                ->with('error', 'আবেদন জমা দিতে সমস্যা হয়েছে। আবার চেষ্টা করুন।');
        }
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
