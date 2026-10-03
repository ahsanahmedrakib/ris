{{--
    Review step rendered between the admission form and its confirmed submit.
    $data      validated payload from the preview request
    $admissionNo reference number the confirmed submit will store
--}}
@php
    // Optional fields the applicant left blank are dropped so the summary stays
    // short; required fields always have a value by this point.
    $groups = [
        'ছাত্র/ছাত্রীর তথ্য' => [
            'শ্রেণি' => 'class_level',
            'শিক্ষাবর্ষ' => 'academic_year',
            'নাম (বাংলায়)' => 'student_name_bn',
            'নাম (ইংরেজিতে)' => 'student_name_en',
            'জন্ম তারিখ' => 'dob',
            'বয়স' => 'age',
            'জাতীয়তা' => 'nationality',
            'ধর্ম' => 'religion',
            'ব্লাড গ্রুপ' => 'blood_group',
        ],
        'পিতা-মাতার তথ্য' => [
            'পিতার নাম (বাংলায়)' => 'father_name_bn',
            'পিতার নাম (ইংরেজিতে)' => 'father_name_en',
            'পিতার পেশা' => 'father_occupation',
            'মাতার নাম (বাংলায়)' => 'mother_name_bn',
            'মাতার নাম (ইংরেজিতে)' => 'mother_name_en',
            'মাতার পেশা' => 'mother_occupation',
        ],
        'যোগাযোগের তথ্য' => [
            'বর্তমান ঠিকানা' => 'present_address',
            'স্থায়ী ঠিকানা' => 'permanent_address',
            'ফোন/মোবাইল' => 'phone',
            'ইমেইল' => 'email',
            'জরুরি প্রয়োজনে ফোন' => 'emergency_contact',
        ],
        'আইনানুগ অভিভাবক' => [
            'নাম' => 'legal_guardian_name',
            'পেশা' => 'legal_guardian_occupation',
            'সম্পর্ক' => 'legal_guardian_relation',
            'ঠিকানা' => 'legal_guardian_address',
        ],
        'স্থানীয় অভিভাবক' => [
            'নাম' => 'local_guardian_name',
            'পেশা' => 'local_guardian_occupation',
            'সম্পর্ক' => 'local_guardian_relation',
            'ঠিকানা' => 'local_guardian_address',
            'ফোন' => 'local_guardian_phone',
        ],
        'পূর্ববর্তী শ্রেণির তথ্য' => [
            'পূর্ববর্তী প্রতিষ্ঠানের নাম' => 'prev_school_name',
            'পূর্ববর্তী প্রতিষ্ঠানের ঠিকানা' => 'prev_school_address',
            'রোল নং' => 'prev_roll_no',
            'পূর্ববর্তী ফল' => 'prev_marks',
        ],
        'রেফারেন্স' => [
            'রেফারেন্সের নাম' => 'reference',
            'রেফারেন্সের ফোন' => 'reference_phone',
        ],
    ];

    $readable = function (mixed $value): string {
        if (is_array($value)) {
            $value = implode(', ', array_filter($value, fn ($item) => $item !== null && $item !== ''));
        }

        return trim((string) $value);
    };
@endphp

{{-- Reference number --}}
<div class="rounded-xl border border-ris-primary/25 bg-ris-primary/5 px-4 py-3 mb-5 text-center">
    <p class="text-xs text-gray-500 mb-1">আবেদন নম্বর</p>
    <p class="font-heading font-bold text-2xl text-ris-primary tracking-wider">
        {{ $admissionNo ?? '—' }}
    </p>
</div>

{{-- Photo: the client fills this in from its own local preview --}}
<div class="flex items-center gap-4 mb-5 pb-5 border-b border-gray-100">
    <img id="admissionReviewPhoto" src="" alt="ছাত্র/ছাত্রীর ছবি"
        class="w-20 h-20 rounded-lg object-cover border border-gray-200 hidden" />
    <div class="text-sm">
        <p class="font-medium text-gray-900">{{ $data['student_name_bn'] ?? '' }}</p>
        <p class="text-gray-500">{{ $data['student_name_en'] ?? '' }}</p>
    </div>
</div>

<div class="space-y-5">
    @foreach ($groups as $heading => $fields)
        @php
            $rows = collect($fields)
                ->map(fn ($key) => [$key, $readable($data[$key] ?? null)])
                ->filter(fn ($pair) => $pair[1] !== '')
                ->values();
        @endphp

        @if ($rows->isNotEmpty())
            <div>
                <p class="text-xs font-semibold text-ris-primary uppercase tracking-wide mb-2">
                    {{ $heading }}
                </p>
                <dl class="space-y-1.5 text-sm">
                    @foreach ($rows as [$key, $value])
                        <div class="flex gap-3">
                            <dt class="w-44 shrink-0 text-gray-500">{{ $key }}</dt>
                            <dd class="flex-1 font-medium text-gray-900 wrap-break-word">
                                {{ $key === 'dob' ? \Illuminate\Support\Carbon::parse($value)->format('d/m/Y') : $value }}
                            </dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        @endif
    @endforeach
</div>

<p class="mt-5 text-xs text-gray-500 bg-yellow-50 border border-yellow-200 rounded p-3">
    নিশ্চিত করে জমা দিলে এই নম্বরটি আপনার আবেদন নম্বর হিসেবে সংরক্ষিত হবে এবং ফরমটি ডাউনলোড করা যাবে।
</p>