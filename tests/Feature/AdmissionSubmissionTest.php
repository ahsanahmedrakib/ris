<?php

namespace Tests\Feature;

use App\Models\Admission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdmissionSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public const VALID_DATA = [
        'academic_year' => '২৬',
        'student_name_bn' => 'মোঃ রাকিব হাসান',
        'student_name_en' => 'MD. RAKIB HASAN',
        'dob' => '2019-05-10',
        'age' => '৬',
        'nationality' => 'বাংলাদেশী',
        'religion' => 'ইসলাম',
        'blood_group' => 'O+',
        'father_name_bn' => 'আব্দুল করিম',
        'father_name_en' => 'ABDUL KARIM',
        'father_occupation' => 'চাকরিজীবী',
        'mother_name_bn' => 'ফাতেমা বেগম',
        'mother_name_en' => 'FATEMA BEGUM',
        'mother_occupation' => 'গৃহিণী',
        'present_address' => 'গোপালগঞ্জ, বাংলাদেশ',
        'permanent_address' => 'গোপালগঞ্জ, বাংলাদেশ',
        'phone' => '01712345678',
        'emergency_contact' => '01712345679',
        'legal_guardian_name' => 'মোঃ করিম উদ্দিন',
        'legal_guardian_occupation' => 'ব্যবসায়ী',
        'legal_guardian_relation' => 'চাচা',
        'legal_guardian_address' => 'গোপালগঞ্জ',
        'local_guardian_name' => 'মোঃ রহমান',
        'local_guardian_occupation' => 'শিক্ষক',
        'local_guardian_relation' => 'চাচা',
        'local_guardian_address' => 'গোপালগঞ্জ',
        'local_guardian_phone' => '01712345680',
        'prev_school_name' => 'সরকারি প্রাথমিক বিদ্যালয়',
        'prev_school_address' => 'গোপালগঞ্জ',
        'prev_marks' => '৮০',
        'reference' => 'মোঃ আহমেদ',
        'reference_phone' => '01712345681',
        'class_level' => ['নার্সারি'],
        'batch' => ['প্রভাতী'],
    ];

    #[Test]
    public function admission_page_loads_with_form(): void
    {
        $this->get(route('admission'))
            ->assertOk()
            ->assertSee('প্রাথমিক আবেদন ফরম')
            ->assertSee('যাচাই করে জমা দিন')
            ->assertSee('ফিরে যান', false)
            ->assertSee('নিশ্চিত করে জমা দিন');
    }

    private static function toBanglaDigits(string $number): string
    {
        return strtr($number, [
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
    }

    #[Test]
    public function admission_page_defaults_academic_year_to_next_year_from_march_through_december(): void
    {
        $nextYearTwoDigits = self::toBanglaDigits((string) ((now()->addYear()->year) % 100));

        foreach ([3, 6, 12] as $month) {
            $this->travelTo(now()->setDate(now()->year, $month, 15));

            $this->get(route('admission'))
                ->assertOk()
                ->assertSee('value="'.$nextYearTwoDigits.'"', false);
        }
    }

    #[Test]
    public function admission_page_defaults_academic_year_to_current_year_in_january_and_february(): void
    {
        $currentYearTwoDigits = self::toBanglaDigits((string) (now()->year % 100));

        foreach ([1, 2] as $month) {
            $this->travelTo(now()->setDate(now()->year, $month, 15));

            $this->get(route('admission'))
                ->assertOk()
                ->assertSee('value="'.$currentYearTwoDigits.'"', false);
        }
    }

    #[Test]
    public function next_admission_number_uses_class_and_academic_year_format(): void
    {
        $year = now()->format('n') >= 3 ? now()->addYear()->format('y') : now()->format('y');

        $this->assertSame("{$year}-1001", Admission::nextAdmissionNo('১ম শ্রেণি'));
        $this->assertSame("{$year}-1001", Admission::nextAdmissionNo('১ম'));
        $this->assertSame("{$year}-2001", Admission::nextAdmissionNo('২য় শ্রেণি'));
        $this->assertSame("{$year}-5001", Admission::nextAdmissionNo('৫ম'));
        $this->assertSame("{$year}-P001", Admission::nextAdmissionNo('প্লে'));
        $this->assertSame("{$year}-N001", Admission::nextAdmissionNo('নার্সারি'));
        $this->assertSame("{$year}-K001", Admission::nextAdmissionNo('কেজি'));
        $this->assertNull(Admission::nextAdmissionNo(null));
    }

    #[Test]
    public function next_admission_number_increments_serially_per_class(): void
    {
        $year = now()->format('n') >= 3 ? now()->addYear()->format('y') : now()->format('y');

        Admission::factory()->create(['admission_no' => "{$year}-1001"]);
        Admission::factory()->create(['admission_no' => "{$year}-1002"]);

        $this->assertSame("{$year}-1003", Admission::nextAdmissionNo('১ম শ্রেণি'));
        $this->assertSame("{$year}-2001", Admission::nextAdmissionNo('২য় শ্রেণি'));
        $this->assertSame("{$year}-P001", Admission::nextAdmissionNo('প্লে'));
    }

    #[Test]
    public function public_form_validates_required_inputs(): void
    {
        $this->post(route('admission.store'), [])
            ->assertSessionHasErrors([
                'academic_year',
                'student_name_bn',
                'student_name_en',
                'dob',
                'age',
                'nationality',
                'religion',
                'blood_group',
                'father_name_bn',
                'father_name_en',
                'father_occupation',
                'mother_name_bn',
                'mother_name_en',
                'mother_occupation',
                'present_address',
                'permanent_address',
                'phone',
                'emergency_contact',
                'legal_guardian_name',
                'legal_guardian_occupation',
                'legal_guardian_relation',
                'legal_guardian_address',
                'local_guardian_name',
                'local_guardian_occupation',
                'local_guardian_relation',
                'local_guardian_address',
                'local_guardian_phone',
                'reference',
                'reference_phone',
                'student_photo',
            ]);

        $this->assertDatabaseCount('admissions', 0);
    }

    #[Test]
    public function store_assigns_a_reference_number_and_pdf_link_to_public_submissions(): void
    {
        Storage::fake('public');

        $response = $this->post(route('admission.store'), [
            ...self::VALID_DATA,
            'student_photo' => UploadedFile::fake()->image('student.jpg', 200, 200),
            'email' => 'test@example.com',
        ]);

        $response->assertRedirect(route('admission'));

        $admission = Admission::first();

        // VALID_DATA applies to নার্সারি, whose class key is N.
        $this->assertSame('27-N001', $admission->admission_no);
        $this->assertNotEmpty($admission->pdf_token);
        $this->assertDatabaseCount('admissions', 1);
    }

    #[Test]
    public function store_returns_json_for_ajax_requests(): void
    {
        Storage::fake('public');

        $this->post(route('admission.store'), [
            ...self::VALID_DATA,
            'student_photo' => UploadedFile::fake()->image('student.jpg', 200, 200),
        ], [
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ])
            ->assertOk()
            ->assertJson(fn ($json) => $json
                ->where('message', fn ($m) => str_contains($m, 'সফলভাবে জমা হয়েছে'))
                ->where('admission_no', '27-N001')
                ->where('pdf_url', fn ($url) => str_contains($url, '/admission/pdf/'))
                ->etc());

        $this->assertDatabaseCount('admissions', 1);
    }

    #[Test]
    public function store_returns_json_validation_errors_for_ajax_requests(): void
    {
        $this->post(route('admission.store'), [], [
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['student_name_bn', 'father_name_bn', 'student_photo']);
    }

    #[Test]
    public function preview_renders_the_review_and_a_reference_number_without_storing_anything(): void
    {
        Storage::fake('public');

        $response = $this->post(route('admission.preview'), self::VALID_DATA, [
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertOk()
            ->assertJsonPath('admission_no', '27-N001')
            ->assertJson(fn ($json) => $json
                ->where('review', fn ($html) => str_contains($html, 'মোঃ রাকিব হাসান')
                    && str_contains($html, 'আব্দুল করিম')
                    && str_contains($html, '27-N001')
                    // The openReview() handler looks this up by id to copy the
                    // locally previewed photo into the review panel.
                    && str_contains($html, 'id="admissionReviewPhoto"'))
                ->etc());

        $this->assertDatabaseCount('admissions', 0);
    }

    #[Test]
    public function preview_reserves_the_reference_number_the_confirmed_submit_stores(): void
    {
        Storage::fake('public');

        $this->post(route('admission.preview'), self::VALID_DATA, [
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ])->assertOk();

        $this->post(route('admission.store'), [
            ...self::VALID_DATA,
            'student_photo' => UploadedFile::fake()->image('student.jpg', 200, 200),
        ], [
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ])->assertOk()->assertJsonPath('admission_no', '27-N001');

        $this->assertSame('27-N001', Admission::first()->admission_no);
    }

    #[Test]
    public function a_display_format_date_is_rejected_rather_than_silently_read_as_us_month_first(): void
    {
        // 10/05/2019 is 10 May to a human but 5 October to strtotime, and the
        // old 'date' rule accepted it, so every confirmed submit that still
        // carried the display format stored the wrong birth date.
        $this->post(route('admission.preview'), [
            ...self::VALID_DATA,
            'dob' => '10/05/2019',
        ], [
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['dob']);

        $this->assertDatabaseCount('admissions', 0);
    }

    #[Test]
    public function an_iso_date_is_stored_as_that_exact_day(): void
    {
        Storage::fake('public');

        $this->post(route('admission.store'), [
            ...self::VALID_DATA,
            'dob' => '2019-05-10',
            'student_photo' => UploadedFile::fake()->image('student.jpg', 200, 200),
        ], [
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ])->assertOk();

        $this->assertSame('2019-05-10', Admission::first()->dob->toDateString());
    }

    #[Test]
    public function an_unknown_class_is_rejected_instead_of_yielding_a_blank_reference(): void
    {
        $this->post(route('admission.preview'), [
            ...self::VALID_DATA,
            'class_level' => ['মানিফেস্ট'],
        ], [
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['class_level.0']);

        $this->assertNull(session('admission.reference_no'));
        $this->assertDatabaseCount('admissions', 0);
    }

    #[Test]
    public function an_impossible_date_is_rejected(): void
    {
        foreach (['2019-02-30', '2019-13-01', 'not-a-date'] as $dob) {
            $this->post(route('admission.preview'), [
                ...self::VALID_DATA,
                'dob' => $dob,
            ], [
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
            ])
                ->assertStatus(422)
                ->assertJsonValidationErrors(['dob']);
        }

        $this->assertDatabaseCount('admissions', 0);
    }

    #[Test]
    public function preview_retries_do_not_consume_the_confirmed_submit_budget(): void
    {
        // Preview and store used to share one 3-per-hour bucket, so a single
        // confirmed submission cost two attempts and the applicant hit
        // "too many attempts" on their second try.
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('admission.preview'), self::VALID_DATA, [
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
            ])->assertOk();
        }

        Storage::fake('public');

        for ($i = 0; $i < 3; $i++) {
            $this->post(route('admission.store'), [
                ...self::VALID_DATA,
                'phone' => '0171234567'.$i,
                'student_photo' => UploadedFile::fake()->image("s{$i}.jpg", 200, 200),
            ], [
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
            ])->assertOk();
        }

        $this->assertDatabaseCount('admissions', 3);
    }

    #[Test]
    public function the_confirmed_submit_route_is_still_rate_limited(): void
    {
        Storage::fake('public');

        foreach (range(1, 3) as $i) {
            $this->post(route('admission.store'), [
                ...self::VALID_DATA,
                'phone' => '0171234567'.$i,
                'student_photo' => UploadedFile::fake()->image("s{$i}.jpg", 200, 200),
            ], [
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
            ])->assertOk();
        }

        $this->post(route('admission.store'), [
            ...self::VALID_DATA,
            'phone' => '01712345679',
            'student_photo' => UploadedFile::fake()->image('s4.jpg', 200, 200),
        ], [
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ])->assertStatus(429);

        $this->assertDatabaseCount('admissions', 3);
    }

    #[Test]
    public function preview_rejects_an_incomplete_form_with_validation_errors(): void
    {
        $this->post(route('admission.preview'), [
            'student_name_bn' => 'শুধু নাম',
        ], [
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['class_level', 'student_name_en', 'dob']);
    }

    #[Test]
    public function admission_pdf_is_reachable_with_its_token_and_404s_otherwise(): void
    {
        Storage::fake('public');

        $this->post(route('admission.store'), [
            ...self::VALID_DATA,
            'student_photo' => UploadedFile::fake()->image('student.jpg', 200, 200),
        ], [
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ])->assertOk();

        $admission = Admission::first();

        $response = $this->get(route('admission.pdf', ['token' => $admission->pdf_token]))
            ->assertOk()
            // Same sheet the admin print view renders, so the applicant's copy
            // and the office copy cannot drift apart.
            ->assertSee('ভর্তি আবেদন ফরম')
            ->assertSee('ভর্তি স্মারক')
            ->assertSee('ভর্তি সংক্রান্ত তথ্য')
            ->assertSee('অভিভাবকের বিবরণ')
            ->assertSee('রেফারেন্স ও ফোন')
            ->assertSee('ভর্তি নং')
            ->assertSee($admission->admission_no);

        // Two A4 sheets: the form plus the ভর্তি স্মারক receipt.
        $this->assertSame(2, substr_count($response->getContent(), 'w-[210mm] min-h-[297mm]'));

        // Print hands off to the browser, which honours the @page/A4 rules.
        // Neither button may use an onclick, which the production CSP kills.
        $response->assertSee('printSheetBtn', false)
            ->assertDontSee('onclick=', false);

        // Download saves the file straight to disk via html2pdf. Both the bundle
        // and the config that makes it work on this sheet must be present: A4
        // pages, and one single pagination strategy so the form and the receipt
        // stay two pages instead of collapsing into one.
        $content = $response->getContent();

        $response->assertSee('downloadSheetBtn', false)
            ->assertSee('html2pdf.bundle.min.js', false)
            ->assertSee('প্রিন্ট করুন')
            ->assertSee('ফরম ডাউনলোড করুন (PDF)')
            ->assertSee("format: 'a4'", false);

        // 'avoid-all' keeps the sheet one continuous flow and lets jsPDF cut it
        // at the A4 boundaries the min-h-[297mm] pages are already sized for.
        // Asking for explicit breaks instead ('css'/'legacy' plus an after
        // selector) split the canvas into three groups with an empty one in the
        // middle, and the trim below then deleted the ভর্তি স্মারক with it.
        $response->assertSee("pagebreak: { mode: ['avoid-all'] }", false)
            ->assertDontSee("mode: ['css', 'legacy']", false);

        // html2canvas does not pass letter-spacing to the browser as a property
        // of the run; it spaces the glyphs one by one, which throws away the
        // shaping of a Bangla cluster and pulls the conjuncts apart in the PDF
        // (the heading came out as "ভরতি আবেদন অফ্রম"). The export has to drop
        // tracking in the clone, while the sheet itself keeps it so the page on
        // screen and the browser print are unchanged.
        $response->assertSee('letter-spacing: normal !important', false)
            ->assertSee('word-spacing: normal !important', false);
        $this->assertStringContainsString('tracking-wide', $content);

        // html2pdf rounds the canvas height up, so an exactly-two-page sheet
        // can export as three pages with a blank sliver at the end. The
        // pipeline must therefore be run by hand so the extra page can be
        // dropped before the file is written.
        $response->assertSee('.toPdf()', false)
            ->assertSee('getNumberOfPages()', false)
            ->assertSee('deletePage(', false)
            ->assertSee("querySelectorAll('.page-container').length", false)
            ->assertSee('pdf.save(filename)', false);

        // Printing still relies on the browser honouring the @page/A4 rules.
        $this->assertStringContainsString('page-break-after: always', $content);
        $this->assertStringContainsString('id="admissionSheet"', $content);

        // Both pages sign off with the head teacher's signature, printed from the
        // same asset the scholarship form uses. It has to sit above the ruled
        // caption rather than replace it, and each image must be the one that
        // belongs to the caption following it.
        $this->assertSame(2, substr_count($content, 'assets/signature.png'));
        $this->assertSame(2, substr_count($content, 'স্কুল কর্তৃপক্ষের স্বাক্ষর'));

        $offset = 0;
        for ($i = 1; $i <= 2; $i++) {
            $image = strpos($content, 'assets/signature.png', $offset);
            $this->assertNotFalse($image, "Signature #{$i} is missing.");

            $caption = strpos($content, 'স্কুল কর্তৃপক্ষের স্বাক্ষর', $image);
            $this->assertNotFalse($caption, "Signature #{$i} has no caption after it.");
            $this->assertLessThan(400, $caption - $image, "Signature #{$i} is not directly above its caption.");

            $offset = $caption + 1;
        }

        // html2canvas silently drops images wrapped in an `inline-block` element
        // inside a flex row: the export finished with no error but the student
        // photo box was blank. The photo frame has to stay a block.
        $this->assertStringNotContainsString('bg-gray-50 inline-block', $content);

        // html2canvas aborts the whole export on Tailwind 4's oklch() colours, so
        // every colour on the page needs a hex value. Derived from the rendered
        // markup rather than hardcoded, so adding a coloured utility without an
        // override fails here instead of silently breaking the download.
        preg_match_all('/--(?:color-)?([a-z]+-\d+|white|black)\s*:\s*([^;]+);/', $content, $overrides);
        $overrides = array_combine($overrides[1], $overrides[2]);

        preg_match_all(
            '/(?:bg|text|border|ring|fill|stroke|divide|placeholder|decoration|from|to|via)-((?:slate|gray|zinc|neutral|stone|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose|white|black)(?:-\d+)?)/',
            $content,
            $used
        );

        $this->assertNotEmpty(array_unique($used[1]));

        foreach (array_unique($used[1]) as $colour) {
            $this->assertArrayHasKey($colour, $overrides, "No hex override for --color-{$colour}.");
            $this->assertStringStartsWith('#', $overrides[$colour], "--color-{$colour} is not hex.");
        }

        $this->get(route('admission.pdf', ['token' => str_repeat('a', 32)]))->assertNotFound();
    }

    #[Test]
    public function the_admin_print_view_and_the_applicant_pdf_render_the_same_sheet(): void
    {
        $admission = Admission::factory()->create(['admission_no' => '27-1001']);

        $adminUser = User::factory()->create(['role' => 'admin', 'email' => 'admin@example.com']);

        $admin = $this->actingAs($adminUser)
            ->get(route('admin.admission.pdf', $admission))
            ->assertOk()
            ->getContent();

        $public = $this->get(route('admission.pdf', ['token' => $admission->pdf_token]))
            ->assertOk()
            ->getContent();

        // Compare the printed sheet itself, ignoring each view's own toolbar
        // and any trailing asset tags. Anchoring on the div's class avoids the
        // ".page-container" rule in the @media print block, which appears in
        // <head> and would otherwise match first.
        $sheet = function (string $html): string {
            $start = strpos($html, 'class="page-container');
            $stops = array_filter([
                strpos($html, '</body>', $start),
                strpos($html, '<script', $start),
                // Livewire appends "<!-- Livewire Scripts -->" after the sheet
                // on the admin response.
                strpos($html, '<!--', $start),
            ], fn ($at) => $at !== false);

            return trim(substr($html, $start, min($stops) - $start));
        };

        $this->assertNotSame('', $sheet($admin));
        $this->assertSame($sheet($admin), $sheet($public));

        // ...and both copies must offer the same two actions, so the applicant
        // can download their form without asking the office for a copy.
        foreach (['admin' => $admin, 'public' => $public] as $which => $html) {
            $this->assertStringContainsString('id="printSheetBtn"', $html, "{$which} copy has no print button.");
            $this->assertStringContainsString('id="downloadSheetBtn"', $html, "{$which} copy has no download button.");
            $this->assertStringContainsString('admission-form-27-1001.pdf', $html, "{$which} copy does not name the download after the reference.");
        }
    }

    #[Test]
    public function each_admission_gets_its_own_reference_number(): void
    {
        Storage::fake('public');

        $this->post(route('admission.store'), [
            ...self::VALID_DATA,
            'phone' => '01712345678',
            'student_photo' => UploadedFile::fake()->image('a.jpg', 200, 200),
        ], [
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ])->assertOk();

        $this->post(route('admission.store'), [
            ...self::VALID_DATA,
            'phone' => '01799999999',
            'student_photo' => UploadedFile::fake()->image('b.jpg', 200, 200),
        ], [
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ])->assertOk();

        $this->assertSame(
            ['27-N001', '27-N002'],
            Admission::orderBy('id')->pluck('admission_no')->all()
        );
    }

    #[Test]
    public function reference_numbers_are_scoped_per_class_and_keep_their_shape(): void
    {
        $this->assertSame('27-1001', Admission::nextAdmissionNo('১ম'));
        $this->assertSame('27-5001', Admission::nextAdmissionNo('৫ম'));

        Admission::factory()->create(['admission_no' => Admission::nextAdmissionNo('১ম')]);

        $this->assertSame('27-1002', Admission::nextAdmissionNo('১ম'));
        $this->assertSame('27-5001', Admission::nextAdmissionNo('৫ম'));
    }

    #[Test]
    public function reference_numbers_are_never_reissued_after_a_soft_delete(): void
    {
        $taken = Admission::factory()->create();
        $taken->delete();

        $next = Admission::nextAdmissionNo('১ম');

        // The unique index still holds the deleted number, so the generator
        // has to move past it. Reissuing it would fail the insert and the
        // retry would derive the same doomed value three times.
        $this->assertNotSame($taken->admission_no, $next);

        $admission = Admission::persistWithAdmissionNoRetry(
            fn () => Admission::create([
                'admission_no' => $next,
                'status' => 'pending',
                'student_name_bn' => 'যাচাইকারী',
            ])
        );

        $this->assertNotNull($admission);
        $this->assertSame($next, $admission->admission_no);
    }

    #[Test]
    public function store_saves_uploaded_photo(): void
    {
        Storage::fake('public');

        $this->post(route('admission.store'), [
            ...self::VALID_DATA,
            'student_photo' => UploadedFile::fake()->image('student.jpg', 200, 200),
        ]);

        $photo = Admission::first()->student_photo;

        $this->assertNotNull($photo);
        $this->assertTrue(Storage::disk('public')->exists($photo));
    }

    #[Test]
    public function previous_class_details_are_optional(): void
    {
        Storage::fake('public');

        $data = self::VALID_DATA;
        unset($data['prev_school_name'], $data['prev_school_address'], $data['prev_marks']);

        $this->post(route('admission.store'), [
            ...$data,
            'student_photo' => UploadedFile::fake()->image('student.jpg', 200, 200),
        ])->assertRedirect(route('admission'));

        $this->assertDatabaseCount('admissions', 1);
    }

    #[Test]
    public function reference_signature_is_optional(): void
    {
        Storage::fake('public');

        $data = self::VALID_DATA;
        unset($data['reference_sign']);

        $this->post(route('admission.store'), [
            ...$data,
            'student_photo' => UploadedFile::fake()->image('student.jpg', 200, 200),
        ])->assertRedirect(route('admission'));

        $this->assertDatabaseCount('admissions', 1);
        $this->assertNull(Admission::first()->reference_sign);
    }

    #[Test]
    public function store_rejects_invalid_email(): void
    {
        $this->post(route('admission.store'), [
            ...self::VALID_DATA,
            'student_photo' => UploadedFile::fake()->image('student.jpg', 200, 200),
            'email' => 'not-an-email',
        ])->assertSessionHasErrors('email');

        $this->assertDatabaseCount('admissions', 0);
    }

    #[Test]
    public function store_rejects_invalid_photo_type(): void
    {
        Storage::fake('public');

        $this->post(route('admission.store'), [
            ...self::VALID_DATA,
            'student_photo' => UploadedFile::fake()->create('document.pdf', 100),
        ])->assertSessionHasErrors('student_photo');

        $this->assertDatabaseCount('admissions', 0);
    }
}
