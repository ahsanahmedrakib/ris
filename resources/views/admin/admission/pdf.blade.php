<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ভর্তি আবেদন ফরম - {{ $admission->admission_no ?? '-' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tiro+Bangla:ital@0;1&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Tiro Bangla', serif;
        }

        @page {
            size: A4;
            margin: 0;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                background: white;
                gap: 0;
                padding: 0;
                margin: 0;
            }

            .page-container {
                box-shadow: none;
                border: none;
                margin: 0;
                min-height: 0 !important;
                height: auto !important;
                page-break-after: always;
            }

            .page-container:last-child {
                page-break-after: auto;
            }
        }
    </style>
</head>

<body class="bg-gray-100 flex flex-col items-center py-8 gap-8">

    <div class="no-print mb-6">
        <button onclick="window.print()"
            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow transition">
            Print / Save PDF
        </button>
    </div>

    <!-- ================ PAGE 1: STUDENT ADMISSION FORM (A4) ================ -->
    <div
        class="page-container w-[210mm] min-h-[297mm] bg-white p-4 border border-gray-300 shadow-lg text-black text-sm relative">

        {{-- Header: logo + school name + photo same line --}}
        <div class="flex items-center gap-3 pb-2">
            <div>
                <img src="{{ asset('logo.png') }}" alt="রেশমা ইন্টারন্যাশনাল স্কুল" class="h-14 w-auto object-contain">
            </div>
            <div class="flex-1 text-center">
                <h1 class="text-lg font-bold text-pink-700 tracking-normal">রেশমা ইন্টারন্যাশনাল স্কুল</h1>
                <p class="text-[11px] text-gray-600 font-medium">প্রি-প্রাইমারি / প্রাইমারি স্কুল</p>
                <p class="text-[10px] text-gray-500 mt-0.5">গোপালগঞ্জ &nbsp;|&nbsp; মোবাইল: ০১৬১৯ ০০৭ ০০৬</p>
            </div>
            <div class="shrink-0">
                @if ($admission->student_photo)
                    <div class="border border-pink-700 p-0.5 bg-gray-50 inline-block">
                        <img src="{{ asset('storage/' . $admission->student_photo) }}" alt="শিক্ষার্থীর ছবি"
                            class="w-20 h-20 object-cover">
                    </div>
                @endif
            </div>
        </div>

        {{-- Title --}}
        <div class="my-1.5">
            <div class="flex items-center gap-3">
                <div class="h-0.5 flex-1 bg-pink-700"></div>
                <h2 class="text-lg font-bold text-pink-700 tracking-wide">ভর্তি আবেদন ফরম</h2>
                <div class="h-0.5 flex-1 bg-pink-700"></div>
            </div>
            <p class="text-[10px] text-gray-600 text-center mt-1">
                ভর্তি নং: <span class="font-bold text-xs">{{ $admission->admission_no ?? '—' }}</span>
                &nbsp;|&nbsp; আবেদনের তারিখ:
                {{ $admission->created_at->format('d/m/Y') }}
                &nbsp;|&nbsp; স্ট্যাটাস:
                <span
                    class="font-bold">{{ isset($statuses[$admission->status]) ? $statuses[$admission->status] : $admission->status }}</span>
            </p>
        </div>

        {{-- Section block: ভর্তি সংক্রান্ত তথ্য --}}
        <div class="mt-2 border border-gray-400 rounded overflow-hidden">
            <div class="bg-pink-700 text-white font-medium px-4 py-px text-[12px]">ভর্তি সংক্রান্ত তথ্য</div>
            <div class="p-1.5 grid grid-cols-2 gap-x-5 gap-y-1">
                <div>
                    <div class="flex items-end">
                        <span class="font-medium text-[12px] text-gray-700 whitespace-nowrap">শিক্ষাবর্ষ</span>
                        <div
                            class="border-b border-black grow ml-1.5 pb-px pl-1 font-bold text-sm leading-tight text-center">
                            {{ $admission->academic_year ?? '-' }}</div>
                    </div>
                </div>
                <div>
                    <div class="flex items-end">
                        <span class="font-medium text-[12px] text-gray-700 whitespace-nowrap">শ্রেণি</span>
                        <div
                            class="border-b border-black grow ml-1.5 pb-px pl-1 font-bold text-sm leading-tight text-center">
                            {{ $admission->class_label }}</div>
                    </div>
                </div>
                <div>
                    <div class="flex items-end">
                        <span class="font-medium text-[12px] text-gray-700 whitespace-nowrap">ব্যাচ</span>
                        <div
                            class="border-b border-black grow ml-1.5 pb-px pl-1 font-bold text-sm leading-tight text-center">
                            {{ $admission->batch_label }}</div>
                    </div>
                </div>
                <div>
                    <div class="flex items-end">
                        <span class="font-medium text-[12px] text-gray-700 whitespace-nowrap">রোল নং</span>
                        <div
                            class="border-b border-black grow ml-1.5 pb-px pl-1 font-bold text-sm leading-tight text-center">
                            {{ $admission->roll_no ?? '-' }}</div>
                    </div>
                </div>
                <div>
                    <div class="flex items-end">
                        <span class="font-medium text-[12px] text-gray-700 whitespace-nowrap">সেকশন</span>
                        <div
                            class="border-b border-black grow ml-1.5 pb-px pl-1 font-bold text-sm leading-tight text-center">
                            {{ $admission->section ?? '-' }}</div>
                    </div>
                </div>
                <div>
                    <div class="flex items-end">
                        <span class="font-medium text-[12px] text-gray-700 whitespace-nowrap">ভর্তির তারিখ</span>
                        <div
                            class="border-b border-black grow ml-1.5 pb-px pl-1 font-bold text-sm leading-tight text-center">
                            {{ $admission->admission_date?->format('d/m/Y') ?? '-' }}</div>
                    </div>
                </div>
                <div>
                    <div class="flex items-end">
                        <span class="font-medium text-[12px] text-gray-700 whitespace-nowrap">ফরম সংগ্রহ</span>
                        <div
                            class="border-b border-black grow ml-1.5 pb-px pl-1 font-bold text-sm leading-tight text-center">
                            {{ $admission->form_collect_date?->format('d/m/Y') ?? '-' }}</div>
                    </div>
                </div>
                <div>
                    <div class="flex items-end">
                        <span class="font-medium text-[12px] text-gray-700 whitespace-nowrap">ফরম জমা</span>
                        <div
                            class="border-b border-black grow ml-1.5 pb-px pl-1 font-bold text-sm leading-tight text-center">
                            {{ $admission->form_submit_date?->format('d/m/Y') ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section block: ছাত্র/ছাত্রীর ও পিতা-মাতার বিবরণ --}}
        <div class="mt-2 border border-gray-400 rounded overflow-hidden">
            <div class="bg-pink-700 text-white font-medium px-4 py-px text-[12px]">ছাত্র/ছাত্রীর ও পিতা-মাতার বিবরণ
            </div>
            <div class="p-1.5 grid grid-cols-2 gap-x-5 gap-y-1">
                <div class="col-span-2">
                    <div class="flex items-end">
                        <span class="font-medium text-[12px] text-gray-700 whitespace-nowrap">নাম (বাংলা)</span>
                        <div
                            class="border-b border-black grow ml-1.5 pb-px pl-1 font-bold text-sm leading-tight text-center">
                            {{ $admission->student_name_bn }}</div>
                    </div>
                </div>
                <div class="col-span-2">
                    <div class="flex items-end">
                        <span class="font-medium text-[12px] text-gray-700 whitespace-nowrap">নাম (ইংরেজিতে, বড়
                            অক্ষরে)</span>
                        <div
                            class="border-b border-black grow ml-1.5 pb-px pl-1 font-bold text-sm leading-tight text-center uppercase">
                            {{ $admission->student_name_en ?? '-' }}</div>
                    </div>
                </div>
                <div class="col-span-2 flex gap-5">
                    <div class="flex-1">
                        <div class="flex items-end">
                            <span class="font-medium text-[12px] text-gray-700 whitespace-nowrap">জন্ম তারিখ</span>
                            <div
                                class="border-b border-black grow ml-1.5 pb-px pl-1 font-bold text-sm leading-tight text-center">
                                {{ $admission->dob?->format('d/m/Y') ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-end">
                            <span class="font-medium text-[12px] text-gray-700 whitespace-nowrap">বয়স</span>
                            <div
                                class="border-b border-black grow ml-1.5 pb-px pl-1 font-bold text-sm leading-tight text-center">
                                {{ $admission->age ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-end">
                            <span class="font-medium text-[12px] text-gray-700 whitespace-nowrap">ব্লাড গ্রুপ</span>
                            <div
                                class="border-b border-black grow ml-1.5 pb-px pl-1 font-bold text-sm leading-tight text-center">
                                {{ $admission->blood_group ?? '-' }}</div>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="flex items-end">
                        <span class="font-medium text-[12px] text-gray-700 whitespace-nowrap">জাতীয়তা</span>
                        <div
                            class="border-b border-black grow ml-1.5 pb-px pl-1 font-bold text-sm leading-tight text-center">
                            {{ $admission->nationality ?? '-' }}</div>
                    </div>
                </div>
                <div>
                    <div class="flex items-end">
                        <span class="font-medium text-[12px] text-gray-700 whitespace-nowrap">ধর্ম</span>
                        <div
                            class="border-b border-black grow ml-1.5 pb-px pl-1 font-bold text-sm leading-tight text-center">
                            {{ $admission->religion ?? '-' }}</div>
                    </div>
                </div>
                <div>
                    <div class="flex items-end">
                        <span class="font-medium text-[12px] text-gray-700 whitespace-nowrap">পিতার নাম (বাংলা)</span>
                        <div
                            class="border-b border-black grow ml-1.5 pb-px pl-1 font-bold text-sm leading-tight text-center">
                            {{ $admission->father_name_bn }}</div>
                    </div>
                </div>
                <div>
                    <div class="flex items-end">
                        <span class="font-medium text-[12px] text-gray-700 whitespace-nowrap">মাতার নাম (বাংলা)</span>
                        <div
                            class="border-b border-black grow ml-1.5 pb-px pl-1 font-bold text-sm leading-tight text-center">
                            {{ $admission->mother_name_bn }}</div>
                    </div>
                </div>
                <div>
                    <div class="flex items-end">
                        <span class="font-medium text-[12px] text-gray-700 whitespace-nowrap">পিতার নাম (ইংরেজি)</span>
                        <div
                            class="border-b border-black grow ml-1.5 pb-px pl-1 font-bold text-sm leading-tight text-center uppercase">
                            {{ $admission->father_name_en ?? '-' }}</div>
                    </div>
                </div>
                <div>
                    <div class="flex items-end">
                        <span class="font-medium text-[12px] text-gray-700 whitespace-nowrap">মাতার নাম (ইংরেজি)</span>
                        <div
                            class="border-b border-black grow ml-1.5 pb-px pl-1 font-bold text-sm leading-tight text-center uppercase">
                            {{ $admission->mother_name_en ?? '-' }}</div>
                    </div>
                </div>
                <div>
                    <div class="flex items-end">
                        <span class="font-medium text-[12px] text-gray-700 whitespace-nowrap">পিতার পেশা</span>
                        <div
                            class="border-b border-black grow ml-1.5 pb-px pl-1 font-bold text-sm leading-tight text-center">
                            {{ $admission->father_occupation ?? '-' }}</div>
                    </div>
                </div>
                <div>
                    <div class="flex items-end">
                        <span class="font-medium text-[12px] text-gray-700 whitespace-nowrap">মাতার পেশা</span>
                        <div
                            class="border-b border-black grow ml-1.5 pb-px pl-1 font-bold text-sm leading-tight text-center">
                            {{ $admission->mother_occupation ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section block: যোগাযোগ --}}
        <div class="mt-2 border border-gray-400 rounded overflow-hidden">
            <div class="bg-pink-700 text-white font-medium px-4 py-px text-[12px]">যোগাযোগ</div>
            <div class="p-1.5 grid grid-cols-2 gap-x-5 gap-y-1">
                <div class="col-span-2">
                    <div class="flex items-end">
                        <span class="font-medium text-[12px] text-gray-700 whitespace-nowrap">বর্তমান ঠিকানা</span>
                        <div
                            class="border-b border-black grow ml-1.5 pb-px pl-1 font-bold text-sm leading-tight text-center">
                            {{ $admission->present_address ?? '-' }}</div>
                    </div>
                </div>
                <div class="col-span-2">
                    <div class="flex items-end">
                        <span class="font-medium text-[12px] text-gray-700 whitespace-nowrap">স্থায়ী ঠিকানা</span>
                        <div
                            class="border-b border-black grow ml-1.5 pb-px pl-1 font-bold text-sm leading-tight text-center">
                            {{ $admission->permanent_address ?? '-' }}</div>
                    </div>
                </div>
                <div>
                    <div class="flex items-end">
                        <span class="font-medium text-[12px] text-gray-700 whitespace-nowrap">ফোন/মোবাইল</span>
                        <div
                            class="border-b border-black grow ml-1.5 pb-px pl-1 font-bold text-sm leading-tight text-center">
                            {{ $admission->phone ?? '-' }}</div>
                    </div>
                </div>
                <div>
                    <div class="flex items-end">
                        <span class="font-medium text-[12px] text-gray-700 whitespace-nowrap">ই-মেইল</span>
                        <div
                            class="border-b border-black grow ml-1.5 pb-px pl-1 font-bold text-sm leading-tight text-center">
                            {{ $admission->email ?? '-' }}</div>
                    </div>
                </div>
                <div class="col-span-2">
                    <div class="flex items-end">
                        <span class="font-medium text-[12px] text-gray-700 whitespace-nowrap">জরুরি প্রয়োজনে
                            ফোন</span>
                        <div
                            class="border-b border-black grow ml-1.5 pb-px pl-1 font-bold text-sm leading-tight text-center">
                            {{ $admission->emergency_contact ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section block: অভিভাবকের বিবরণ --}}
        <div class="mt-2 border border-gray-400 rounded overflow-hidden">
            <div class="bg-pink-700 text-white font-medium px-4 py-px text-[12px]">অভিভাবকের বিবরণ</div>
            <div class="p-1.5 grid grid-cols-2 gap-x-5 gap-y-1">
                <div class="col-span-2">
                    <div class="flex items-end">
                        <span class="font-medium text-[12px] text-gray-700 whitespace-nowrap">আইনানুগ অভিভাবকের
                            নাম</span>
                        <div
                            class="border-b border-black grow ml-1.5 pb-px pl-1 font-bold text-sm leading-tight text-center">
                            {{ $admission->legal_guardian_name ?? '-' }}</div>
                    </div>
                </div>
                <div>
                    <div class="flex items-end">
                        <span class="font-medium text-[12px] text-gray-700 whitespace-nowrap">পেশা</span>
                        <div
                            class="border-b border-black grow ml-1.5 pb-px pl-1 font-bold text-sm leading-tight text-center">
                            {{ $admission->legal_guardian_occupation ?? '-' }}</div>
                    </div>
                </div>
                <div>
                    <div class="flex items-end">
                        <span class="font-medium text-[12px] text-gray-700 whitespace-nowrap">সম্পর্ক</span>
                        <div
                            class="border-b border-black grow ml-1.5 pb-px pl-1 font-bold text-sm leading-tight text-center">
                            {{ $admission->legal_guardian_relation ?? '-' }}</div>
                    </div>
                </div>
                <div class="col-span-2">
                    <div class="flex items-end">
                        <span class="font-medium text-[12px] text-gray-700 whitespace-nowrap">ঠিকানা</span>
                        <div
                            class="border-b border-black grow ml-1.5 pb-px pl-1 font-bold text-sm leading-tight text-center">
                            {{ $admission->legal_guardian_address ?? '-' }}</div>
                    </div>
                </div>
                <div class="col-span-2">
                    <div class="flex items-end">
                        <span class="font-medium text-[12px] text-gray-700 whitespace-nowrap">স্থানীয় অভিভাবকের
                            নাম</span>
                        <div
                            class="border-b border-black grow ml-1.5 pb-px pl-1 font-bold text-sm leading-tight text-center">
                            {{ $admission->local_guardian_name ?? '-' }}</div>
                    </div>
                </div>
                <div>
                    <div class="flex items-end">
                        <span class="font-medium text-[12px] text-gray-700 whitespace-nowrap">পেশা</span>
                        <div
                            class="border-b border-black grow ml-1.5 pb-px pl-1 font-bold text-sm leading-tight text-center">
                            {{ $admission->local_guardian_occupation ?? '-' }}</div>
                    </div>
                </div>
                <div>
                    <div class="flex items-end">
                        <span class="font-medium text-[12px] text-gray-700 whitespace-nowrap">সম্পর্ক</span>
                        <div
                            class="border-b border-black grow ml-1.5 pb-px pl-1 font-bold text-sm leading-tight text-center">
                            {{ $admission->local_guardian_relation ?? '-' }}</div>
                    </div>
                </div>
                <div class="col-span-2">
                    <div class="flex items-end">
                        <span class="font-medium text-[12px] text-gray-700 whitespace-nowrap">ফোন</span>
                        <div
                            class="border-b border-black grow ml-1.5 pb-px pl-1 font-bold text-sm leading-tight text-center">
                            {{ $admission->local_guardian_phone ?? '-' }}</div>
                    </div>
                </div>
                <div class="col-span-2">
                    <div class="flex items-end">
                        <span class="font-medium text-[12px] text-gray-700 whitespace-nowrap">ঠিকানা</span>
                        <div
                            class="border-b border-black grow ml-1.5 pb-px pl-1 font-bold text-sm leading-tight text-center">
                            {{ $admission->local_guardian_address ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section block: পূর্ববর্তী শ্রেণির বিবরণ --}}
        <div class="mt-2 border border-gray-400 rounded overflow-hidden">
            <div class="bg-pink-700 text-white font-medium px-4 py-px text-[12px]">পূর্ববর্তী শ্রেণির বিবরণ</div>
            <div class="p-1.5 grid grid-cols-2 gap-x-5 gap-y-1">
                <div class="col-span-2">
                    <div class="flex items-end">
                        <span class="font-medium text-[12px] text-gray-700 whitespace-nowrap">শিক্ষা প্রতিষ্ঠানের
                            নাম</span>
                        <div
                            class="border-b border-black grow ml-1.5 pb-px pl-1 font-bold text-sm leading-tight text-center">
                            {{ $admission->prev_school_name ?? '-' }}</div>
                    </div>
                </div>
                <div class="col-span-2">
                    <div class="flex items-end">
                        <span class="font-medium text-[12px] text-gray-700 whitespace-nowrap">প্রতিষ্ঠানের
                            ঠিকানা</span>
                        <div
                            class="border-b border-black grow ml-1.5 pb-px pl-1 font-bold text-sm leading-tight text-center">
                            {{ $admission->prev_school_address ?? '-' }}</div>
                    </div>
                </div>
                <div>
                    <div class="flex items-end">
                        <span class="font-medium text-[12px] text-gray-700 whitespace-nowrap">শ্রেণির রোল নং</span>
                        <div
                            class="border-b border-black grow ml-1.5 pb-px pl-1 font-bold text-sm leading-tight text-center">
                            {{ $admission->prev_roll_no ?? '-' }}</div>
                    </div>
                </div>
                <div>
                    <div class="flex items-end">
                        <span class="font-medium text-[12px] text-gray-700 whitespace-nowrap">বার্ষিক প্রাপ্ত
                            নম্বর</span>
                        <div
                            class="border-b border-black grow ml-1.5 pb-px pl-1 font-bold text-sm leading-tight text-center">
                            {{ $admission->prev_marks ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section block: রেফারেন্স ও ফোন --}}
        <div class="mt-2 border border-gray-400 rounded overflow-hidden">
            <div class="bg-pink-700 text-white font-medium px-4 py-px text-[12px]">রেফারেন্স ও ফোন</div>
            <div class="p-1.5 grid grid-cols-2 gap-x-5 gap-y-1">
                <div>
                    <div class="flex items-end">
                        <span class="font-medium text-[12px] text-gray-700 whitespace-nowrap">রেফারেন্স</span>
                        <div
                            class="border-b border-black grow ml-1.5 pb-px pl-1 font-bold text-sm leading-tight text-center">
                            {{ $admission->reference ?? '-' }}</div>
                    </div>
                </div>
                <div>
                    <div class="flex items-end">
                        <span class="font-medium text-[12px] text-gray-700 whitespace-nowrap">ফোন/মোবাইল</span>
                        <div
                            class="border-b border-black grow ml-1.5 pb-px pl-1 font-bold text-sm leading-tight text-center">
                            {{ $admission->reference_phone ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Signatures --}}
        <div class="flex justify-between items-end mt-6 gap-10 text-sm font-bold">
            <div class="flex-1 text-center">
                <div class="border-t-2 border-double border-black pt-1.5">ছাত্র/ছাত্রীর অভিভাবকের স্বাক্ষর</div>
            </div>
            <div class="flex-1 text-center">
                <div class="border-t-2 border-double border-black pt-1.5">স্কুল কর্তৃপক্ষের স্বাক্ষর</div>
            </div>
        </div>

    </div>

    <!-- ================ PAGE 2: ভর্তি স্মারক (A4) ================ -->
    <div
        class="page-container w-[210mm] min-h-[297mm] bg-white p-5 border border-gray-300 shadow-lg text-black text-sm relative">

        <div class="flex items-center gap-3 pb-2">
            <div>
                <img src="{{ asset('logo.png') }}" alt="রেশমা ইন্টারন্যাশনাল স্কুল"
                    class="h-14 w-auto object-contain">
            </div>
            <div class="flex-1 text-center">
                <h1 class="text-lg font-bold text-pink-700 tracking-normal">রেশমা ইন্টারন্যাশনাল স্কুল</h1>
                <p class="text-[11px] text-gray-600 font-medium">প্রি-প্রাইমারি / প্রাইমারি স্কুল</p>
                <p class="text-[10px] text-gray-500 mt-0.5">গোপালগঞ্জ &nbsp;|&nbsp; মোবাইল: ০১৬১৯ ০০৭ ০০৬</p>
            </div>
            <div class="shrink-0 text-center">
                @if ($admission->student_photo)
                    <div class="border border-pink-700 p-0.5 bg-gray-50 inline-block">
                        <img src="{{ asset('storage/' . $admission->student_photo) }}" alt="শিক্ষার্থীর ছবি"
                            class="w-20 h-20 object-cover">
                    </div>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-3 my-4">
            <div class="h-0.5 flex-1 bg-pink-700"></div>
            <h2 class="text-lg font-bold text-pink-700 tracking-wide">ভর্তি স্মারক</h2>
            <div class="h-0.5 flex-1 bg-pink-700"></div>
        </div>

        <p class="text-xs text-gray-600 text-center -mt-2 mb-4">
            ভর্তি নং: <span class="font-bold text-sm">{{ $admission->admission_no ?? '—' }}</span>
        </p>

        {{-- Section block: স্মারক বিবরণ --}}
        <div class="mt-2 border border-gray-400 rounded overflow-hidden">
            <div class="bg-pink-700 text-white font-medium px-4 py-px text-[12px]">ভর্তি স্মারক</div>
            <div class="p-3 grid grid-cols-2 gap-x-5 gap-y-3">
                <div class="col-span-2">
                    <div class="flex items-end">
                        <span class="font-medium text-[13px] text-gray-700 whitespace-nowrap">শিক্ষার্থীর নাম</span>
                        <div
                            class="border-b-2 border-black grow ml-2 pb-0.5 pl-1 font-bold text-lg leading-tight text-center">
                            {{ $admission->student_name_bn }}</div>
                    </div>
                </div>
                <div>
                    <div class="flex items-end">
                        <span class="font-medium text-[13px] text-gray-700 whitespace-nowrap">শিক্ষাবর্ষ</span>
                        <div
                            class="border-b-2 border-black grow ml-2 pb-0.5 pl-1 font-bold text-lg leading-tight text-center">
                            {{ $admission->academic_year ?? '-' }}</div>
                    </div>
                </div>
                <div>
                    <div class="flex items-end">
                        <span class="font-medium text-[13px] text-gray-700 whitespace-nowrap">শ্রেণি</span>
                        <div
                            class="border-b-2 border-black grow ml-2 pb-0.5 pl-1 font-bold text-lg leading-tight text-center">
                            {{ $admission->class_label }}</div>
                    </div>
                </div>
                <div>
                    <div class="flex items-end">
                        <span class="font-medium text-[13px] text-gray-700 whitespace-nowrap">ব্যাচ</span>
                        <div
                            class="border-b-2 border-black grow ml-2 pb-0.5 pl-1 font-bold text-lg leading-tight text-center">
                            {{ $admission->batch_label }}</div>
                    </div>
                </div>
                <div>
                    <div class="flex items-end">
                        <span class="font-medium text-[13px] text-gray-700 whitespace-nowrap">রোল নং</span>
                        <div
                            class="border-b-2 border-black grow ml-2 pb-0.5 pl-1 font-bold text-lg leading-tight text-center">
                            {{ $admission->roll_no ?? '-' }}</div>
                    </div>
                </div>
                <div>
                    <div class="flex items-end">
                        <span class="font-medium text-[13px] text-gray-700 whitespace-nowrap">সেকশন</span>
                        <div
                            class="border-b-2 border-black grow ml-2 pb-0.5 pl-1 font-bold text-lg leading-tight text-center">
                            {{ $admission->section ?? '-' }}</div>
                    </div>
                </div>
                <div>
                    <div class="flex items-end">
                        <span class="font-medium text-[13px] text-gray-700 whitespace-nowrap">ভর্তির তারিখ</span>
                        <div
                            class="border-b-2 border-black grow ml-2 pb-0.5 pl-1 font-bold text-lg leading-tight text-center">
                            {{ $admission->admission_date?->format('d/m/Y') ?? '-' }}</div>
                    </div>
                </div>
                <div>
                    <div class="flex items-end">
                        <span class="font-medium text-[13px] text-gray-700 whitespace-nowrap">রেফারেন্স</span>
                        <div
                            class="border-b-2 border-black grow ml-2 pb-0.5 pl-1 font-bold text-lg leading-tight text-center">
                            {{ $admission->reference ?? '-' }}</div>
                    </div>
                </div>
                <div>
                    <div class="flex items-end">
                        <span class="font-medium text-[13px] text-gray-700 whitespace-nowrap">ফোন/মোবাইল</span>
                        <div
                            class="border-b-2 border-black grow ml-2 pb-0.5 pl-1 font-bold text-lg leading-tight text-center">
                            {{ $admission->reference_phone ?? '-' }}</div>
                    </div>
                </div>
            </div>
            <div class="flex justify-end text-base font-bold p-3 mt-3">
                <div class="text-center">
                    <div class="border-t-2 border-double border-black pt-2 px-10">স্কুল কর্তৃপক্ষের স্বাক্ষর</div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
