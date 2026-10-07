<?php

namespace App\Features\Website\Http\Controllers;

use App\Enums\FeeType;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\AboutContent;
use App\Models\AcademicCalendar;
use App\Models\AcademicYear;
use App\Models\Admission;
use App\Models\CampusEvent;
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
use App\Models\ScholarshipRegistration;
use App\Models\ScholarshipSetting;
use App\Models\SchoolStatistic;
use App\Models\Student;
use App\Models\Testimonial;
use App\Notifications\NewSubmission;
use App\Support\JsonLd;
use App\Support\Media;
use App\Support\Seo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WebsiteController extends Controller
{
    public function index(): View
    {
        $statistic = SchoolStatistic::first();

        $stats = $statistic
            ? [
                'total_students' => $statistic->total_students,
                'total_teachers' => $statistic->total_teachers,
                'total_classes' => $statistic->total_classes,
                'total_staff' => $statistic->total_staff,
                'founding_year' => $statistic->founding_year,
                'experience_years' => $statistic->experienceYears(),
            ]
            : SchoolStatistic::defaults() + ['experience_years' => max((int) date('Y') - SchoolStatistic::defaults()['founding_year'], 1)];

        $heroSlides = $this->heroSlides();

        $campusEvents = $this->campusEvents();

        $notices = Notice::where('is_active', true)
            ->where('published_at', '<=', now())
            ->orderByDesc('published_at')
            ->take(5)
            ->get()
            ->map(fn (Notice $notice, int $index): array => [
                'title' => $notice->title,
                'slug' => $notice->slug,
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

        // The FAQ block is the one piece of structured data on this page that can win
        // extra space in the results, so it is declared from the same rows the
        // template renders. A FAQPage node whose questions are not visible on the
        // page is a manual action against the site.
        $faqNode = $faqs->isNotEmpty() ? [JsonLd::faqPage($faqs)] : [];

        $seo = Seo::forCurrentPage(['schema' => $faqNode])->withCanonical(route('home'));

        return view('website.index', compact('stats', 'heroSlides', 'campusEvents', 'notices', 'testimonials', 'testimonialStats', 'galleryItems', 'messages', 'faqs', 'seo'));
    }

    protected function campusEvents(): array
    {
        $news = CampusEvent::where('is_active', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->take(8)
            ->get();

        if ($news->isNotEmpty()) {
            return $news->map(fn (CampusEvent $item): array => [
                'title' => $item->title,
                'slug' => $item->slug,
                'date' => $this->banglaDate($item->date),
                'image' => $item->image ? Media::images()->url($item->image) : null,
            ])->all();
        }

        // Placeholder entries, shown only while no campus event has been added.
        // They have no slug because there is no row behind them to link to.
        return [
            [
                'title' => 'বিজ্ঞান ও প্রযুক্তি মেলা ২০২৬ অনুষ্ঠিত',
                'slug' => null,
                'date' => '৩১ আগস্ট, ২০২৬',
                'image' => null,
            ],
            [
                'title' => 'আন্তর্জাতিক ভাষা দিবস পালন',
                'slug' => null,
                'date' => '২৫ আগস্ট, ২০২৬',
                'image' => null,
            ],
            [
                'title' => 'ক্রীড়া প্রতিযোগিতা ২০২৬',
                'slug' => null,
                'date' => '১৭ আগস্ট, ২০২৬',
                'image' => null,
            ],
            [
                'title' => 'সাংস্কৃতিক অনুষ্ঠান — আমার সোনার বাংলা',
                'slug' => null,
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
            $slides = collect(array_map(fn (array $slide): array => [
                'image' => asset($slide['image']),
                'title' => $slide['title'],
                'subtitle' => $slide['subtitle'],
                'btn_text' => $slide['btn_text'],
                'link' => $slide['link'],
            ], HeroSlide::defaults()));
        } else {
            $slides = $slides->map(fn (HeroSlide $slide): array => [
                'image' => Media::images()->url($slide->image),
                'title' => $slide->title,
                'subtitle' => $slide->subtitle,
                'btn_text' => $slide->btn_text,
                'link' => $slide->link,
            ]);
        }

        if (! ScholarshipSetting::isOpen()) {
            $slides = $slides->reject(fn (array $slide): bool => $slide['link'] === 'scholarship');
        }

        return array_values($slides->all());
    }

    public function testimonials(): View
    {
        $testimonials = Testimonial::where('is_active', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(12);

        $testimonialStats = $this->testimonialStats();

        $seo = Seo::forCurrentPage()->withCanonical(route('testimonials'));

        return view('website.testimonials', compact('testimonials', 'testimonialStats', 'seo'));
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
            $photoPath = Media::storeImage($request->file('photo'), 'testimonial-photos');
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
                route('admin.testimonials.index').'?view='.$testimonial->id,
            );

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'আপনার মতামত জমা হয়েছে। অ্যাডমিনের অনুমোদনের পরে ওয়েবসাইটে প্রকাশিত হবে।',
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

        $seo = Seo::forCurrentPage([
            'schema' => [
                JsonLd::collectionPage(
                    route('gallery'),
                    'Photo Gallery — Resma International School, Gopalganj',
                    'Photographs of the campus, events and academic activities at Resma International School, Gopalganj.',
                ),
            ],
        ])->withCanonical(route('gallery'));

        return view('website.gallery', compact('items', 'categories', 'seo'));
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

        $path = Storage::disk('public_files')->path($academicCalendar->file_path);

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
        $user = $request->user();
        $role = $user->role;

        $classes = ClassRoom::get();
        $exams = Exam::latest('start_date')->get();

        $selectedClass = $request->input('class_id');
        $search = trim((string) $request->input('search'));
        $examId = $request->input('exam_id');

        $examGroups = collect();

        // Results are scoped to whoever is asking: a parent only ever sees their
        // own children, a student only themselves, staff see a whole class.
        $studentQuery = match ($role) {
            UserRole::Parent->value => $user->parentStudents()->with('user')->orderBy('roll_no'),
            UserRole::Student->value => Student::with('user')->where('user_id', $user->id),
            default => Student::with('user')->orderBy('roll_no'),
        };

        if ($role !== UserRole::Parent->value && $role !== UserRole::Student->value && $selectedClass) {
            $studentQuery->where('class_id', $selectedClass);
        }

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

            $examGroups = $allResults->groupBy('exam_id')->map(function ($rows) {
                $exam = $rows->first()->exam;

                // Subjects that actually have results in this exam, ordered by
                // name so subject-wise tables follow the syllabus order.
                $subjects = $rows->map(fn ($result) => $result->subject)
                    ->filter()
                    ->unique('id')
                    ->sortBy('name')
                    ->values();

                // subject_id => student_id => result
                $rowsBySubject = $rows->groupBy('subject_id')
                    ->map(fn ($subjectRows) => $subjectRows->keyBy('student_id'));

                return compact('exam', 'subjects', 'rowsBySubject');
            })->values();
        }

        return view('website.academic.results', compact(
            'classes',
            'exams',
            'students',
            'examGroups',
            'selectedClass',
            'search',
            'examId'
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

        // "Admission 2026" is the single most searched term on a school site
        // alongside the school's own name, so the year is put in both the title
        // and the description rather than being left to go stale each January.
        $admissionYear = $year;
        $title = ' ভর্তি | Admission Form | '.Seo::siteName();
        $description = 'Apply for admission to Resma International School, Gopalganj for the academic year. Fill the online admission form, see the required documents and class-wise fees.';

        $seo = Seo::forCurrentPage([
            'title' => $title,
            'description' => $description,
            'schema' => [JsonLd::educationalProgram()],
        ])->withCanonical(route('admission'));

        return view('website.admission', compact('classes', 'defaultAcademicYear', 'seo'));
    }

    public function storeAdmission(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate($this->admissionRules(), $this->admissionMessages());

        $failureMessage = 'আবেদন জমা দিতে সমস্যা হয়েছে। আবার চেষ্টা করুন।';

        // The same phone and name arriving twice within a few minutes is a double
        // submit (double click, back button, duplicate tab), not two children.
        $recentDuplicate = Admission::where('phone', $validated['phone'] ?? null)
            ->where('student_name_en', $validated['student_name_en'] ?? null)
            ->where('created_at', '>=', now()->subMinutes(10))
            ->exists();

        if ($recentDuplicate) {
            $failureMessage = 'এই আবেদনটি ইতিমধ্যে জমা হয়েছে। দয়া করে আবার জমা দেবেন না।';
        }

        $admission = null;
        $photoPath = null;

        // class_level is stored as an array but nextAdmissionNo() keys on a single
        // class, the same convention the admin list uses when it reads class_level[0].
        $classLevel = $validated['class_level'][0] ?? null;

        // The review step reserved this number; a retry after a serial collision
        // re-derives instead, because the winner now holds the reserved one.
        $reservedNo = $request->session()->pull('admission.reference_no');

        if ($reservedNo !== null && ! str_starts_with((string) $reservedNo, (string) Admission::referencePrefix($classLevel))) {
            $reservedNo = null;
        }

        $usedReservedNo = false;

        if (! $recentDuplicate) {
            try {
                if ($request->hasFile('student_photo')) {
                    $photoPath = Media::storeImage($request->file('student_photo'), 'admissions');
                }

                $admission = Admission::persistWithAdmissionNoRetry(function () use ($validated, $classLevel, $photoPath, $reservedNo, &$usedReservedNo): Admission {
                    $admissionNo = $reservedNo !== null && ! $usedReservedNo
                        ? $reservedNo
                        : Admission::nextAdmissionNo($classLevel);

                    $usedReservedNo = true;

                    return Admission::create([
                        'admission_no' => $admissionNo,
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
                });

                // The helper reports and swallows its own failures, so a null here
                // means the upload has no row to belong to.
                if (! $admission instanceof Admission && $photoPath) {
                    Storage::disk('public')->delete($photoPath);
                }
            } catch (\Throwable $e) {
                report($e);

                if ($photoPath) {
                    Storage::disk('public')->delete($photoPath);
                }
            }
        }

        if (! $admission instanceof Admission) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $failureMessage], 422);
            }

            return back()->withInput()->with('error', $failureMessage);
        }

        // The admission is committed at this point. A notification failure is a
        // side effect and must never tell the applicant their form was lost,
        // which previously led to resubmissions and duplicate applications.
        try {
            NewSubmission::sendToAdmins(
                'admission',
                'নতুন ভর্তি আবেদন',
                $admission->student_name_bn.' ('.$admission->phone.') ভর্তি আবেদন করেছেন।',
                route('admin.admission.index').'?view='.$admission->id,
            );
        } catch (\Throwable $e) {
            report($e);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'admission_no' => $admission->admission_no,
                'pdf_url' => route('admission.pdf', ['token' => $admission->pdf_token]),
                'message' => 'আপনার ভর্তি আবেদন সফলভাবে জমা হয়েছে। আমরা শীঘ্রই আপনার সাথে যোগাযোগ করব।',
            ]);
        }

        return redirect()->route('admission')
            ->with('success', 'আপনার ভর্তি আবেদন সফলভাবে জমা হয়েছে। আমরা শীঘ্রই আপনার সাথে যোগাযোগ করব।');
    }

    /**
     * @return array<string, string>
     */
    private function admissionRules(bool $requirePhoto = true): array
    {
        $rules = [
            'academic_year' => 'required|string|max:10',
            'roll_no' => 'nullable|string|max:20',
            'section' => 'nullable|string|max:50',
            'batch' => 'nullable|array',
            'batch.*' => 'string|max:50',
            'admission_date' => 'nullable|date',
            'form_collect_date' => 'nullable|date',
            'form_submit_date' => 'nullable|date',
            'class_level' => 'required|array|min:1',
            'class_level.*' => [
                'required',
                'string',
                'max:50',
                // Without this an unknown class sails through validation and
                // then resolves to no CLASS_KEYS entry, which left the
                // reference number null and printed as a dash on the form.
                Rule::in(array_keys(Admission::CLASS_KEYS)),
            ],
            'student_name_bn' => 'required|string|max:255',
            'student_name_en' => 'required|string|max:255',
            // The date mask submits ISO. Pinning the format stops a stray dd/mm/yyyy
            // from being quietly read as m/d/y and storing the wrong birth date.
            'dob' => 'required|date_format:Y-m-d',
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
        ];

        // The review step posts the applicant's text without re-uploading the
        // photo; the confirmed submit still requires it.
        if (! $requirePhoto) {
            unset($rules['student_photo']);
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    private function admissionMessages(): array
    {
        return [
            'academic_year.required' => 'শিক্ষাবর্ষ আবশ্যক।',
            'class_level.required' => 'শ্রেণি নির্বাচন করুন।',
            'class_level.*.required' => 'শ্রেণি নির্বাচন করুন।',
            'class_level.*.in' => 'নির্বাচিত শ্রেণিটি সঠিক নয়।',
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
        ];
    }

    /**
     * Review step: validate what the applicant typed and hand back a rendered
     * summary plus the reference number the confirmed submit will use.
     */
    public function admissionPreview(Request $request): JsonResponse
    {
        $validated = $request->validate(
            $this->admissionRules(requirePhoto: false),
            $this->admissionMessages()
        );

        $referenceNo = Admission::nextAdmissionNo($validated['class_level'][0] ?? null);

        // Held so the number shown on the review step is the number the
        // confirmed submit stores, instead of a second derivation that could
        // land on a serial a concurrent applicant just claimed.
        $request->session()->put('admission.reference_no', $referenceNo);

        return response()->json([
            'admission_no' => $referenceNo,
            'review' => view('website.admission.review', [
                'data' => $validated,
                'admissionNo' => $referenceNo,
            ])->render(),
        ]);
    }

    public function admissionPdf(string $token): View
    {
        return view('website.admission.pdf', [
            'admission' => Admission::where('pdf_token', $token)->firstOrFail(),
            'statuses' => Admission::STATUSES,
        ]);
    }

    public function scholarship(): View
    {
        $year = now()->year;
        $setting = ScholarshipSetting::current();
        $title = "মেধাবৃত্তি | Merit Scholarship {$year} | ".Seo::siteName();
        $description = "Apply for the merit scholarship at Resma International School, Gopalganj for {$year}. Open to meritorious and financially disadvantaged students. Online registration, free to apply.";

        $seo = Seo::forCurrentPage([
            'title' => $title,
            'description' => $description,
            'schema' => [
                JsonLd::collectionPage(
                    route('scholarship'),
                    'Merit Scholarship — Resma International School, Gopalganj',
                    $description,
                ),
            ],
        ])->withCanonical(route('scholarship'));

        return view('website.scholarship', compact('seo', 'setting', 'year'));
    }

    // admit download
    public function scholarshipPdf(string $registration_no, string $token): View
    {
        $registration = ScholarshipRegistration::where('registration_no', $registration_no)
            ->firstOrFail();

        abort_unless(hash_equals((string) $registration->pdf_token, $token), 404);

        return view('admin.scholarship.pdf', [
            'registration' => $registration,
            'classes' => ScholarshipRegistration::CLASSES,
        ]);
    }

    public function contact(): View
    {
        return view('website.contact', [
            'seo' => Seo::forCurrentPage(['schema' => [JsonLd::contactPage()]])
                ->withCanonical(route('contact')),
        ]);
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
            route('admin.contact-messages.index').'?view='.$contact->id,
        );

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'আপনার বার্তা সফলভাবে পাঠানো হয়েছে। আমরা শীঘ্রই যোগাযোগ করব।',
            ]);
        }

        return redirect()->route('contact')->with('success', 'আপনার বার্তা সফলভাবে পাঠানো হয়েছে। আমরা শীঘ্রই যোগাযোগ করব।');
    }

    public function notices(): View
    {
        $notices = Notice::where('is_active', true)
            ->where('published_at', '<=', now())
            ->orderByDesc('published_at')
            ->paginate(10);

        // The board is a list of articles, so it is declared as one. Every page
        // of the pagination is the same page to a crawler, so the node carries
        // only this page's notices and the canonical URL below strips the query.
        $seo = Seo::forCurrentPage([
            'schema' => [
                JsonLd::collectionPage(
                    route('notices'),
                    'Notice Board — Resma International School, Gopalganj',
                    'Exam schedules, holidays and admission notices from Resma International School, Gopalganj.',
                    $notices->getCollection()->map(
                        fn (Notice $notice): string => route('notices.single', $notice->slug)
                    )->all(),
                ),
            ],
        ])->withCanonical(route('notices'));

        return view('website.notices', compact('notices', 'seo'));
    }

    public function campusEventsIndex(): View
    {
        $items = CampusEvent::where('is_active', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(9);

        // The same collections page the notices board uses: this lists every
        // campus event a visitor can reach, so it is declared as a
        // collection and the canonical URL strips the pagination query.
        $seo = Seo::forCurrentPage([
            'schema' => [
                JsonLd::collectionPage(
                    route('campus-events'),
                    'Campus Events — Resma International School, Gopalganj',
                    'Events and activities at Resma International School, Gopalganj: science fairs, cultural programmes, sports days and excursions.',
                    $items->getCollection()->map(
                        fn (CampusEvent $item): string => route('campus-events.single', $item->slug)
                    )->all(),
                ),
            ],
        ])->withCanonical(route('campus-events'));

        return view('website.campus-events', compact('items', 'seo'));
    }

    public function noticeSingle(string $slug): View
    {
        // The same conditions the notice board lists under, so a notice that is
        // hidden there cannot be opened by guessing its address either.
        $notice = Notice::where('slug', $slug)
            ->where('is_active', true)
            ->where('published_at', '<=', now())
            ->with('publisher')
            ->firstOrFail();

        $related = Notice::where('is_active', true)
            ->where('published_at', '<=', now())
            ->whereKeyNot($notice->getKey())
            ->orderByDesc('published_at')
            ->take(4)
            ->get();

        // The notice's own title and opening become the metadata. A generic
        // "Notice" description here would make every notice in the index read
        // identically in the results, which is worse than no description.
        $seo = Seo::forCurrentPage([
            'title' => Seo::composeTitle($notice->title),
            'description' => Seo::excerpt($notice->content, 160),
            'type' => 'article',
            'schema' => [
                JsonLd::article(
                    $notice->title,
                    $notice->content,
                    null,
                    ($notice->published_at ?? $notice->created_at)?->toAtomString(),
                ),
            ],
        ])->andCrumb(['name' => $notice->title]);

        return view('website.notice-single', compact('notice', 'related', 'seo'));
    }

    public function campusEventSingle(string $slug): View
    {
        $item = CampusEvent::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $related = CampusEvent::where('is_active', true)
            ->whereKeyNot($item->getKey())
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->take(4)
            ->get();

        $image = $item->image ? Media::images()->url($item->image) : null;

        $seo = Seo::forCurrentPage([
            'title' => Seo::composeTitle($item->title),
            'description' => Seo::excerpt($item->description ?: $item->title, 160),
            'type' => 'article',
            'image' => $image,
            'schema' => [
                JsonLd::article(
                    $item->title,
                    $item->description,
                    $image,
                    $item->date?->toAtomString(),
                ),
            ],
        ])->andCrumb(['name' => $item->title]);

        return view('website.campus-events-single', compact('item', 'related', 'seo'));
    }
}
