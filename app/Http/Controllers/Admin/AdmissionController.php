<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\User;
use App\Support\NumberConverter;
use App\Support\UniqueConstraintViolation;
use App\Support\XlsxExport;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdmissionController extends Controller
{
    public function index(Request $request): View
    {
        $query = Admission::with('creator')->with('student');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('admission_no', 'like', "%{$search}%")
                    ->orWhere('student_name_bn', 'like', "%{$search}%")
                    ->orWhere('student_name_en', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('father_name_bn', 'like', "%{$search}%")
                    ->orWhere('mother_name_bn', 'like', "%{$search}%");
            });
        }
        $perPage = (int) $request->input('per_page', 10);

        if (! in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $admissions = $query->latest()->paginate($perPage)->withQueryString();

        $admittedNos = Student::pluck('admission_no')->map(fn ($no) => (string) $no)->all();

        $defaultAcademicYear = strtr((string) ((now()->format('n') >= 3 ? now()->addYear()->year : now()->year) % 100), [
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

        return view('admin.admission.index', [
            'admissions' => $admissions,
            'defaultAcademicYear' => $defaultAcademicYear,
            'statuses' => Admission::STATUSES,
            'batches' => Admission::BATCHES,
            'classes' => Admission::CLASS_OPTIONS,
            'admittedNos' => $admittedNos,
            'breadcrumbs' => ['ভর্তি আবেদন' => null],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules(), $this->messages());

        $photoPath = null;

        if ($request->hasFile('student_photo')) {
            $photoPath = $request->file('student_photo')->store('admissions', 'public');
        }

        $admission = $this->persistWithAdmissionNoRetry(
            fn (): Admission => Admission::create([
                ...$this->payload($validated),
                'admission_no' => Admission::nextAdmissionNo($validated['class_level'] ?? null),
                'student_photo' => $photoPath,
            ])
        );

        if (! $admission instanceof Admission) {
            return back()->withInput()
                ->with('error', 'ভর্তি আবেদন তৈরি করতে সমস্যা হয়েছে। আবার চেষ্টা করুন।');
        }

        return redirect()->route('admin.admission.index')
            ->with('success', 'ভর্তি আবেদন ('.$admission->admission_no.') সফলভাবে তৈরি হয়েছে।');
    }

    /**
     * Persist an admission whose admission_no is derived from a read-then-
     * increment serial, retrying when another writer claims the same number.
     *
     * Two admins saving the same class at the same moment can pick the same
     * serial. The unique index rejects the loser; each retry re-reads the serial
     * which now includes the winner's row. The closure must therefore derive the
     * number afresh on every attempt rather than reuse an in-memory value.
     *
     * @param  callable(): Admission  $persist
     */
    private function persistWithAdmissionNoRetry(callable $persist): ?Admission
    {
        for ($attempt = 1; $attempt <= 3; $attempt++) {
            try {
                DB::beginTransaction();

                $admission = $persist();

                DB::commit();

                return $admission;
            } catch (QueryException $e) {
                DB::rollBack();

                if ($attempt < 3 && UniqueConstraintViolation::matches($e)) {
                    continue;
                }

                report($e);
            } catch (\Throwable $e) {
                DB::rollBack();

                report($e);
            }

            return null;
        }

        return null;
    }

    public function show(Admission $admission)
    {
        $admission->load('creator');

        return response()->json([
            'id' => $admission->id,
            'admission_no' => $admission->admission_no,
            'status' => $admission->status,
            'status_label' => Admission::STATUSES[$admission->status] ?? $admission->status,
            'academic_year' => $admission->academic_year,
            'roll_no' => $admission->roll_no,
            'section' => $admission->section,
            'batch' => $admission->batch_label,
            'admission_date' => $admission->admission_date?->format('d/m/Y'),
            'form_collect_date' => $admission->form_collect_date?->format('d/m/Y'),
            'form_submit_date' => $admission->form_submit_date?->format('d/m/Y'),
            'class_level' => $admission->class_label,
            'student_name_bn' => $admission->student_name_bn,
            'student_name_en' => $admission->student_name_en,
            'dob' => $admission->dob?->format('d/m/Y'),
            'age' => $admission->age,
            'nationality' => $admission->nationality,
            'religion' => $admission->religion,
            'blood_group' => $admission->blood_group,
            'father_name_bn' => $admission->father_name_bn,
            'father_name_en' => $admission->father_name_en,
            'father_occupation' => $admission->father_occupation,
            'mother_name_bn' => $admission->mother_name_bn,
            'mother_name_en' => $admission->mother_name_en,
            'mother_occupation' => $admission->mother_occupation,
            'present_address' => $admission->present_address,
            'permanent_address' => $admission->permanent_address,
            'phone' => $admission->phone,
            'email' => $admission->email,
            'emergency_contact' => $admission->emergency_contact,
            'legal_guardian_name' => $admission->legal_guardian_name,
            'legal_guardian_occupation' => $admission->legal_guardian_occupation,
            'legal_guardian_relation' => $admission->legal_guardian_relation,
            'legal_guardian_address' => $admission->legal_guardian_address,
            'local_guardian_name' => $admission->local_guardian_name,
            'local_guardian_occupation' => $admission->local_guardian_occupation,
            'local_guardian_relation' => $admission->local_guardian_relation,
            'local_guardian_address' => $admission->local_guardian_address,
            'local_guardian_phone' => $admission->local_guardian_phone,
            'prev_school_name' => $admission->prev_school_name,
            'prev_school_address' => $admission->prev_school_address,
            'prev_roll_no' => $admission->prev_roll_no,
            'prev_marks' => $admission->prev_marks,
            'reference' => $admission->reference,
            'reference_phone' => $admission->reference_phone,
            'reference_sign' => $admission->reference_sign,
            'student_photo' => $admission->student_photo ? asset('storage/'.ltrim($admission->student_photo, '/')) : null,
            'created_at' => $admission->created_at->format('d/m/Y h:i A'),
            'creator_name' => $admission->creator?->name ?? 'অনলাইন (শিক্ষার্থী)',
            'admitted' => Student::where('admission_no', $admission->admission_no)->exists(),
        ]);
    }

    public function edit(Admission $admission)
    {
        return response()->json([
            'id' => $admission->id,
            'admission_no' => $admission->admission_no,
            'status' => $admission->status,
            'academic_year' => $admission->academic_year,
            'roll_no' => $admission->roll_no,
            'section' => $admission->section,
            'batch' => $admission->batch[0] ?? null,
            'admission_date' => $admission->admission_date?->format('d/m/Y'),
            'class_level' => $admission->class_level[0] ?? null,
            'student_name_bn' => $admission->student_name_bn,
            'student_name_en' => $admission->student_name_en,
            'dob' => $admission->dob?->format('d/m/Y'),
            'age' => $admission->age,
            'nationality' => $admission->nationality,
            'religion' => $admission->religion,
            'blood_group' => $admission->blood_group,
            'father_name_bn' => $admission->father_name_bn,
            'father_occupation' => $admission->father_occupation,
            'mother_name_bn' => $admission->mother_name_bn,
            'mother_occupation' => $admission->mother_occupation,
            'present_address' => $admission->present_address,
            'permanent_address' => $admission->permanent_address,
            'phone' => $admission->phone,
            'email' => $admission->email,
            'emergency_contact' => $admission->emergency_contact,
            'father_name_en' => $admission->father_name_en,
            'mother_name_en' => $admission->mother_name_en,
            'form_collect_date' => $admission->form_collect_date?->format('d/m/Y'),
            'form_submit_date' => $admission->form_submit_date?->format('d/m/Y'),
            'legal_guardian_name' => $admission->legal_guardian_name,
            'legal_guardian_occupation' => $admission->legal_guardian_occupation,
            'legal_guardian_relation' => $admission->legal_guardian_relation,
            'legal_guardian_address' => $admission->legal_guardian_address,
            'local_guardian_name' => $admission->local_guardian_name,
            'local_guardian_occupation' => $admission->local_guardian_occupation,
            'local_guardian_relation' => $admission->local_guardian_relation,
            'local_guardian_address' => $admission->local_guardian_address,
            'local_guardian_phone' => $admission->local_guardian_phone,
            'prev_school_name' => $admission->prev_school_name,
            'prev_school_address' => $admission->prev_school_address,
            'prev_roll_no' => $admission->prev_roll_no,
            'prev_marks' => $admission->prev_marks,
            'reference' => $admission->reference,
            'reference_phone' => $admission->reference_phone,
            'reference_sign' => $admission->reference_sign,
            'student_photo' => $admission->student_photo ? asset('storage/'.$admission->student_photo) : null,
        ]);
    }

    public function update(Request $request, Admission $admission): RedirectResponse
    {
        $rules = $this->rules();

        if ($admission->student_photo) {
            $rules['student_photo'] = 'nullable|image|mimes:jpeg,jpg,png|max:2048';
        }

        $validated = $request->validate($rules, $this->messages());

        // Captured before the loop: a failed update leaves the colliding value
        // on the model in memory, which would make every retry reuse it.
        $existingAdmissionNo = $admission->admission_no;
        $photoPath = $admission->student_photo;

        if ($request->hasFile('student_photo')) {
            if ($admission->student_photo) {
                Storage::disk('public')->delete($admission->student_photo);
            }

            $photoPath = $request->file('student_photo')->store('admissions', 'public');
        }

        $updated = $this->persistWithAdmissionNoRetry(function () use ($admission, $validated, $existingAdmissionNo, $photoPath): Admission {
            $admissionNo = $existingAdmissionNo ?: Admission::nextAdmissionNo($validated['class_level'] ?? null);

            $admission->update([
                ...$this->payload($validated),
                'admission_no' => $admissionNo,
                'student_photo' => $photoPath,
            ]);

            return $admission;
        });

        if (! $updated instanceof Admission) {
            return back()->withInput()
                ->with('error', 'ভর্তি আবেদন আপডেট করতে সমস্যা হয়েছে। আবার চেষ্টা করুন।');
        }

        return redirect()->route('admin.admission.index')
            ->with('success', 'ভর্তি আবেদন ('.$updated->admission_no.') সফলভাবে আপডেট হয়েছে।');
    }

    public function updateStatus(Request $request, Admission $admission): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        try {
            $admission->update(['status' => $validated['status']]);

            $statusLabel = Admission::STATUSES[$validated['status']];

            return back()->with('success', 'স্ট্যাটাস "'.$statusLabel.'"-এ পরিবর্তন করা হয়েছে।');
        } catch (\Exception $e) {
            return back()->with('error', 'স্ট্যাটাস আপডেট করতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }

    public function admit(Request $request, Admission $admission): RedirectResponse
    {
        $validated = $request->validate([
            'gender' => 'required|in:male,female,other',
        ], [
            'gender.required' => 'লিঙ্গ নির্বাচন আবশ্যক।',
            'gender.in' => 'সঠিক লিঙ্গ নির্বাচন করুন।',
        ]);

        if ($admission->status !== 'approved') {
            return back()->with('error', 'শুধুমাত্র অনুমোদিত ভর্তি আবেদনকে ভর্তি করা যাবে।');
        }

        if (Student::where('admission_no', $admission->admission_no)->exists()) {
            return back()->with('error', 'এই শিক্ষার্থী ('.$admission->admission_no.') ইতিমধ্যে ভর্তি হয়ে গেছে।');
        }

        $classLevel = $admission->class_level[0] ?? null;

        if (! $classLevel) {
            return back()->with('error', 'শ্রেণি নির্ধারণ করা যায়নি।');
        }

        $class = ClassRoom::where('name', $classLevel)
            ->orWhere('name', trim(str_replace('শ্রেণি', '', $classLevel)))
            ->first();

        if (! $class) {
            return back()->with('error', '"'.$classLevel.'" শ্রেণির সাথে মিলে যাওয়া শ্রেণি পাওয়া যায়নি। আগে শ্রেণি তৈরি করুন।');
        }

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $admission->student_name_en ?: ($admission->student_name_bn ?: 'শিক্ষার্থী'),
                'email' => $this->uniqueStudentEmail($admission),
                'phone' => $admission->phone,
                'password' => Str::random(10),
                'role' => UserRole::Student->value,
                'avatar' => $admission->student_photo,
                'is_active' => true,
            ]);

            $academicYear = NumberConverter::toAscii((string) ($admission->academic_year ?? '26'));
            $classNum = $this->getClassNumber($classLevel);
            $rollNum = str_pad((string) ($admission->roll_no ?: 1), 3, '0', STR_PAD_LEFT);
            $studentId = 'STU-'.$academicYear.'-'.$classNum.$rollNum;

            Student::create([
                'user_id' => $user->id,
                'admission_no' => $admission->admission_no,
                'student_id' => $studentId,
                'class_id' => $class->id,
                'section' => $admission->section ?: '-',
                'roll_no' => (int) ($admission->roll_no ?: 0),
                'date_of_birth' => $admission->dob,
                'gender' => $validated['gender'],
                'blood_group' => $admission->blood_group,
                'address' => $admission->present_address ?: ($admission->permanent_address ?: ''),
                'guardian_name' => $admission->father_name_bn ?: ($admission->legal_guardian_name ?: ''),
                'guardian_phone' => $admission->phone ?: ($admission->local_guardian_phone ?: ''),
                'guardian_email' => $admission->email,
                'is_active' => true,
            ]);

            DB::commit();

            return redirect()->route('admin.admission.index')
                ->with('success', 'শিক্ষার্থী "'.$admission->student_name_bn.'" সফলভাবে ভর্তি করা হয়েছে। ID: '.$studentId);
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->with('error', 'শিক্ষার্থী ভর্তি করতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }

    private function getClassNumber(?string $classLabel): string
    {
        if (! $classLabel) {
            return '0';
        }

        $playLabels = ['প্লে', 'play', 'preschool', 'pre-primary', 'pp', 'p'];
        if (in_array(trim($classLabel), $playLabels, true)) {
            return 'P';
        }

        $banglaMap = [
            '০' => '0', '১' => '1', '২' => '2', '৩' => '3', '৪' => '4',
            '৫' => '5', '৬' => '6', '৭' => '7', '৮' => '8', '৯' => '9',
        ];

        $firstChar = mb_substr($classLabel, 0, 1);
        if (isset($banglaMap[$firstChar])) {
            return $banglaMap[$firstChar];
        }

        if (is_numeric($firstChar)) {
            return (string) $firstChar;
        }

        return '0';
    }

    private function uniqueStudentEmail(Admission $admission): string
    {
        if ($admission->email && ! User::where('email', $admission->email)->exists()) {
            return $admission->email;
        }

        $base = Str::slug((string) $admission->student_name_en, '-');

        if ($base === '') {
            $base = 'student-'.$admission->id;
        }

        $email = $base.'@ris.local';
        $i = 2;

        while (User::where('email', $email)->exists()) {
            $email = $base.'-'.$i.'@ris.local';
            $i++;
        }

        return $email;
    }

    public function print(Admission $admission): View
    {
        return view('admin.admission.pdf', [
            'admission' => $admission,
            'batches' => Admission::BATCHES,
            'classes' => Admission::CLASS_OPTIONS,
            'statuses' => Admission::STATUSES,
        ]);
    }

    public function downloadAll(Request $request)
    {
        $query = Admission::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('admission_no', 'like', "%{$search}%")
                    ->orWhere('student_name_bn', 'like', "%{$search}%")
                    ->orWhere('student_name_en', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $admissions = $query->latest()->get();

        $rows = $admissions->map(fn ($admission, $index) => [
            $index + 1,
            $admission->admission_no,
            $admission->student_name_bn,
            $admission->class_label,
            $admission->batch_label,
            $admission->roll_no ?? '-',
            $admission->phone ?? '-',
            $admission->email ?? '-',
            Admission::STATUSES[$admission->status] ?? $admission->status,
            $admission->created_at->format('d/m/Y'),
        ])->all();

        return XlsxExport::download(
            ['ক্রমিক', 'ভর্তি নং', 'শিক্ষার্থীর নাম', 'শ্রেণি', 'ব্যাচ', 'রোল', 'ফোন', 'ইমেইল', 'স্ট্যাটাস', 'তারিখ'],
            $rows,
            'admissions_'.now('Asia/Dhaka')->format('Y-m-d_H-i').'.xlsx',
        );
    }

    public function destroy(Admission $admission): RedirectResponse
    {
        try {
            $admissionNo = $admission->admission_no;

            $admission->delete();

            return redirect()->route('admin.admission.index')
                ->with('success', 'ভর্তি আবেদন ('.$admissionNo.') ট্র্যাশে পাঠানো হয়েছে।');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'ভর্তি আবেদন মুছে ফেলতে সমস্যা হয়েছে। '.$e->getMessage());
        }
    }

    /** @return array<string, mixed> */
    private function rules(): array
    {
        return [
            'academic_year' => 'required|string|max:10',
            'roll_no' => 'required|string|max:20',
            'section' => 'required|string|max:50',
            'batch' => 'required|string|max:50',
            'admission_date' => 'required|date',
            'form_collect_date' => 'required|date',
            'form_submit_date' => 'required|date',
            'class_level' => 'required|string|max:50',
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
            'status' => 'required|in:pending,approved,rejected',
        ];
    }

    /** @return array<string, string> */
    private function messages(): array
    {
        return [
            'academic_year.required' => 'শিক্ষাবর্ষ আবশ্যক।',
            'roll_no.required' => 'রোল নং আবশ্যক।',
            'section.required' => 'সেকশন আবশ্যক।',
            'batch.required' => 'ব্যাচ নির্বাচন করুন।',
            'admission_date.required' => 'ভর্তির তারিখ আবশ্যক।',
            'form_collect_date.required' => 'ফরম সংগ্রহের তারিখ আবশ্যক।',
            'form_submit_date.required' => 'ফরম জমা দেয়ার তারিখ আবশ্যক।',
            'class_level.required' => 'শ্রেণি নির্বাচন করুন।',
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

    /** @return array<string, mixed> */
    private function payload(array $validated): array
    {
        return [
            'status' => $validated['status'],
            'academic_year' => $validated['academic_year'] ?? null,
            'roll_no' => $validated['roll_no'] ?? null,
            'section' => $validated['section'] ?? null,
            'batch' => isset($validated['batch']) ? [$validated['batch']] : null,
            'admission_date' => $validated['admission_date'] ?? null,
            'form_collect_date' => $validated['form_collect_date'] ?? null,
            'form_submit_date' => $validated['form_submit_date'] ?? null,
            'class_level' => isset($validated['class_level']) ? [$validated['class_level']] : null,
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
            'created_by' => Auth::id(),
        ];
    }
}
