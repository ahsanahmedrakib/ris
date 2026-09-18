<?php

namespace App\Features\Website\Http\Controllers;

use App\Enums\FeeType;
use App\Http\Controllers\Controller;
use App\Models\AboutContent;
use App\Models\AcademicCalendar;
use App\Models\AcademicYear;
use App\Models\Admission;
use App\Models\CampusNews;
use App\Models\ClassRoom;
use App\Models\ContactMessage;
use App\Models\CoreValue;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Faq;
use App\Models\GalleryItem;
use App\Models\HeroSlide;
use App\Models\Message;
use App\Models\Notice;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Testimonial;
use App\Models\User;
use App\Notifications\NewSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
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

        $heroSlides = $this->heroSlides();

        $campusNews = $this->campusNews();

        $notices = Notice::where('is_active', true)
            ->where('published_at', '<=', now())
            ->orderByDesc('published_at')
            ->take(5)
            ->get()
            ->map(fn (Notice $notice, int $index): array => [
                'title' => $notice->title,
                'date' => $this->banglaDate($notice->published_at ?? $notice->created_at),
                'category' => $notice->category ?? 'general',
                'category_label' => Notice::CATEGORIES[$notice->category ?? 'general'] ?? 'সাধারণ',
                'highlight' => $index === 0,
            ])
            ->values()
            ->all();

        $testimonials = Testimonial::where('is_active', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->take(6)
            ->get();

        $testimonialStats = $this->testimonialStats();

        $galleryItems = GalleryItem::where('is_active', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->take(8)
            ->get();

        $messages = Message::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $faqs = Faq::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('website.index', compact('stats', 'heroSlides', 'campusNews', 'notices', 'testimonials', 'testimonialStats', 'galleryItems', 'messages', 'faqs'));
    }

    protected function campusNews(): array
    {
        $news = CampusNews::where('is_active', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->take(8)
            ->get();

        if ($news->isNotEmpty()) {
            return $news->map(fn (CampusNews $item): array => [
                'title' => $item->title,
                'date' => $this->banglaDate($item->date),
                'image' => $item->image ? Storage::url($item->image) : null,
            ])->all();
        }

        return [
            [
                'title' => 'বিজ্ঞান ও প্রযুক্তি মেলা ২০২৬ অনুষ্ঠিত',
                'date' => '৩১ আগস্ট, ২০২৬',
                'image' => null,
            ],
            [
                'title' => 'আন্তর্জাতিক ভাষা দিবস পালন',
                'date' => '২৫ আগস্ট, ২০২৬',
                'image' => null,
            ],
            [
                'title' => 'ক্রীড়া প্রতিযোগিতা ২০২৬',
                'date' => '১৭ আগস্ট, ২০২৬',
                'image' => null,
            ],
            [
                'title' => 'সাংস্কৃতিক অনুষ্ঠান — আমার সোনার বাংলা',
                'date' => '১০ আগস্ট, ২০২৬',
                'image' => null,
            ],
        ];
    }

    protected function banglaDate(?\DateTimeInterface $date): ?string
    {
        if (! $date) {
            return null;
        }

        $months = ['জানুয়ারি', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর'];
        $en = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $bn = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
        $carbon = Carbon::instance($date);

        return str_replace($en, $bn, (string) $carbon->day).' '.$months[$carbon->month - 1].' '.str_replace($en, $bn, (string) $carbon->year);
    }

    protected function banglaNumber(int|float $number): string
    {
        $en = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $bn = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];

        return str_replace($en, $bn, (string) $number);
    }

    protected function heroSlides(): array
    {
        $slides = HeroSlide::where('is_active', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        if ($slides->isEmpty()) {
            return array_map(fn (array $slide): array => [
                'image' => asset($slide['image']),
                'title' => $slide['title'],
                'subtitle' => $slide['subtitle'],
                'btn_text' => $slide['btn_text'],
                'link' => $slide['link'],
            ], HeroSlide::defaults());
        }

        return $slides->map(fn (HeroSlide $slide): array => [
            'image' => Storage::url($slide->image),
            'title' => $slide->title,
            'subtitle' => $slide->subtitle,
            'btn_text' => $slide->btn_text,
            'link' => $slide->link,
        ])->all();
    }

    public function testimonials(): View
    {
        $testimonials = Testimonial::where('is_active', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(12);

        $testimonialStats = $this->testimonialStats();

        return view('website.testimonials', compact('testimonials', 'testimonialStats'));
    }

    /**
     * Aggregate rating stats for the testimonial sections.
     *
     * @return array{total: int, average: int|float|null, bangla_total: string, bangla_average: string}
     */
    protected function testimonialStats(): array
    {
        $approved = Testimonial::where('is_active', true);
        $total = $approved->count();
        $average = $total > 0 ? round($approved->avg('rating'), 1) : null;

        return [
            'total' => $total,
            'average' => $average,
            'bangla_total' => $this->banglaNumber($total),
            'bangla_average' => $average !== null ? $this->banglaNumber($average) : '০',
        ];
    }

    public function storeTestimonial(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'designation' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'message' => 'required|string',
            'rating' => 'nullable|integer|min:1|max:5',
        ], [
            'name.required' => 'আপনার নাম আবশ্যক।',
            'message.required' => 'আপনার মন্তব্য লিখুন।',
            'photo.image' => 'সঠিক ছবি আপলোড করুন।',
            'photo.mimes' => 'ছবির ফরম্যাট jpeg, jpg, png বা webp হতে হবে।',
            'photo.max' => 'ছবির আকার ২ এমবির বেশি হতে পারবে না।',
            'rating.min' => 'রেটিং ১ থেকে ৫ এর মধ্যে হতে হবে।',
            'rating.max' => 'রেটিং ১ থেকে ৫ এর মধ্যে হতে হবে।',
        ]);

        $photoPath = null;

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('testimonial-photos', 'public');
        }

        try {
            $testimonial = Testimonial::create([
                'name' => $validated['name'],
                'designation' => $validated['designation'] ?? null,
                'photo' => $photoPath,
                'message' => $validated['message'],
                'rating' => $validated['rating'] ?? 5,
                'sort_order' => 0,
                'is_active' => false,
            ]);

            NewSubmission::sendToAdmins(
                'testimonial',
                'নতুন মতামত',
                $testimonial->name.' একটি মতামত জমা দিয়েছেন।',
                route('admin.testimonials.show', $testimonial),
            );

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'আপনার মতামত জমা হয়েছে। প্রশাসকের অনুমোদনের পরে ওয়েবসাইটে প্রকাশিত হবে।',
                ]);
            }

            return redirect()->route('testimonials')
                ->with('success', 'আপনার মতামত জমা হয়েছে। অনুমোদনের পরে এটি ওয়েবসাইটে দেখানো হবে।');
        } catch (\Exception $e) {
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'মতামত জমা দিতে সমস্যা হয়েছে। আবার চেষ্টা করুন।',
                ], 500);
            }

            return back()->withInput()
                ->with('error', 'মতামত জমা দিতে সমস্যা হয়েছে. '.$e->getMessage());
        }
    }

    public function gallery(): View
    {
        $items = GalleryItem::where('is_active', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(24);

        $categories = GalleryItem::where('is_active', true)->pluck('category')->filter()->unique()->values();

        return view('website.gallery', compact('items', 'categories'));
    }

    public function academicCalendar(): View
    {
        $calendars = AcademicCalendar::where('is_active', true)
            ->orderBy('year')
            ->orderBy('sort_order')
            ->get();

        return view('website.academic.calendar', compact('calendars'));
    }

    public function academicCalendarPdf(AcademicCalendar $academicCalendar)
    {
        abort_unless($academicCalendar->is_active, 404);

        $path = Storage::disk('public')->path($academicCalendar->file_path);

        abort_unless(file_exists($path), 404);

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$academicCalendar->file_name.'"',
        ]);
    }

    public function academicFees(): View
    {
        $academicYear = AcademicYear::where('is_current', true)->first();

        $classes = collect();

        if ($academicYear) {
            $classes = ClassRoom::with([
                'feeStructures' => fn ($query) => $query
                    ->where('academic_year_id', $academicYear->id)
                    ->orderBy('fee_type'),
            ])
                ->orderBy('name')
                ->get()
                ->filter(fn (ClassRoom $class) => $class->feeStructures->isNotEmpty())
                ->values();
        }

        $feeTypes = collect(FeeType::cases())
            ->filter(fn (FeeType $type) => $classes->contains(
                fn (ClassRoom $class) => $class->feeStructures->contains('fee_type', $type->value)
            ))
            ->values();

        return view('website.academic.fees', compact('classes', 'feeTypes', 'academicYear'));
    }

    public function academicResults(Request $request): View
    {
        $classes = ClassRoom::get();
        $exams = Exam::latest('start_date')->get();

        $selectedClass = $request->input('class_id');
        $search = trim((string) $request->input('search'));
        $examId = $request->input('exam_id');

        $students = new Collection;
        $examGroups = collect();

        if ($selectedClass) {
            $studentQuery = Student::with('user')
                ->where('class_id', $selectedClass)
                ->orderBy('roll_no');

            if ($request->filled('roll_no')) {
                $studentQuery->where('roll_no', $request->input('roll_no'));
            }

            if ($search !== '') {
                $studentQuery->where(function ($q) use ($search) {
                    $q->where('admission_no', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($uq) use ($search) {
                            $uq->where('name', 'like', "%{$search}%");
                        });
                });
            }

            $students = $studentQuery->get();

            if ($students->isNotEmpty()) {
                $resultQuery = ExamResult::with(['subject', 'exam'])
                    ->whereIn('student_id', $students->pluck('id'));

                if ($examId) {
                    $resultQuery->where('exam_id', $examId);
                }

                $allResults = $resultQuery->orderBy('exam_id')->orderBy('subject_id')->get();

                $examGroups = $allResults->groupBy('exam_id')->map(function ($rows) use ($students) {
                    $exam = $rows->first()->exam;
                    $subjectIds = $rows->pluck('subject_id')->unique();

                    $rowsByStudent = $rows->groupBy('student_id')
                        ->map(fn ($studentRows) => $studentRows->keyBy('subject_id'));

                    $studentTotals = $students->mapWithKeys(function ($student) use ($rowsByStudent) {
                        $studentRows = $rowsByStudent->get($student->id, collect());
                        $total = $studentRows->sum('marks_obtained');

                        return [$student->id => $total];
                    });

                    return compact('exam', 'subjectIds', 'rowsByStudent', 'studentTotals');
                })->values();
            }
        }

        $subjects = $selectedClass
            ? Subject::where('class_id', $selectedClass)->orderBy('name')->get()
            : collect();

        return view('website.academic.results', compact(
            'classes', 'exams', 'students', 'examGroups', 'subjects',
            'selectedClass', 'search', 'examId'
        ));
    }

    public function academicFacilities(): View
    {
        return view('website.academic.facilities');
    }

    public function about(): View
    {
        $defaults = AboutContent::defaults();

        $mission = AboutContent::where('type', 'mission')->first()
            ?? AboutContent::make(['type' => 'mission', ...$defaults['mission']]);

        $vision = AboutContent::where('type', 'vision')->first()
            ?? AboutContent::make(['type' => 'vision', ...$defaults['vision']]);

        if (CoreValue::exists()) {
            $coreValues = CoreValue::where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();
        } else {
            $coreValues = collect(CoreValue::defaults())
                ->map(fn (array $value, int $index): CoreValue => CoreValue::make([
                    ...$value,
                    'is_active' => true,
                    'sort_order' => $index,
                ]));
        }

        return view('website.about', compact('mission', 'vision', 'coreValues'));
    }

    public function admission(): View
    {
        $classes = ClassRoom::get();

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
            'class_level' => 'required|array|min:1',
            'class_level.*' => 'required|string|max:50',
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
            'class_level.required' => 'শ্রেণি নির্বাচন করুন।',
            'class_level.*.required' => 'শ্রেণি নির্বাচন করুন।',
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

        $photoPath = null;

        try {
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

            NewSubmission::sendToAdmins(
                'admission',
                'নতুন ভর্তি আবেদন',
                $admission->student_name_bn.' ('.$admission->phone.') ভর্তি আবেদন করেছেন।',
                route('admin.admission.show', $admission),
            );

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
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ], [
            'name.required' => 'নাম আবশ্যক।',
            'name.max' => 'নাম ২৫৫ অক্ষরের বেশি হতে পারবে না।',
            'email.email' => 'সঠিক ইমেইল দিন।',
            'email.max' => 'ইমেইল ২৫৫ অক্ষরের বেশি হতে পারবে না।',
            'phone.required' => 'ফোন নম্বর আবশ্যক।',
            'phone.max' => 'ফোন নম্বর ২০ অক্ষরের বেশি হতে পারবে না।',
            'subject.required' => 'বিষয় আবশ্যক।',
            'subject.max' => 'বিষয় ২৫৫ অক্ষরের বেশি হতে পারবে না।',
            'message.required' => 'বার্তা আবশ্যক।',
        ]);

        $contact = ContactMessage::create($validated);

        NewSubmission::sendToAdmins(
            'contact',
            'নতুন কনটাক্ট মেসেজ',
            $contact->name.' ('.$contact->subject.') মেসেজ পাঠিয়েছেন।',
            route('admin.contact-messages.show', $contact),
        );

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
