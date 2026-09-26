@extends('layouts.website')

@section('title', 'ভর্তি — রেশমা ইন্টারন্যাশনাল স্কুল')

@section('content')

    {{-- Hero --}}
    <section class="bg-linear-to-r from-ris-dark via-ris-accent to-ris-light py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal">
            <h1 class="font-heading font-bold text-3xl sm:text-4xl text-white">ভর্তি তথ্য</h1>
            <p class="mt-3 text-white/70 text-lg">২০২৬ শিক্ষাবর্ষের ভর্তি প্রক্রিয়া সম্পর্কে জানুন</p>
        </div>
    </section>

    {{-- Admission Application Form --}}
    <section class="py-12 sm:py-16 bg-ris-gray-50 relative overflow-hidden">
        <div
            class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-px bg-linear-to-r from-transparent via-ris-primary/40 to-transparent pointer-events-none">
        </div>
        <div class="absolute top-10 right-10 w-24 h-24 border-2 border-ris-primary/20 rounded-full pointer-events-none">
        </div>
        <div class="absolute top-24 right-24 w-16 h-16 bg-ris-accent/15 rounded-xl rotate-12 pointer-events-none"></div>
        <div
            class="absolute bottom-12 left-6 w-20 h-20 border border-ris-primary/20 rotate-45 rounded-lg pointer-events-none">
        </div>
        <div class="absolute bottom-28 left-28 w-6 h-6 bg-ris-primary/15 rotate-12 pointer-events-none"></div>
        <div
            class="absolute top-1/3 right-5 w-px h-40 bg-linear-to-b from-ris-primary/30 to-transparent pointer-events-none">
        </div>
        <div class="absolute top-10 left-4 w-10 h-10 border-2 border-ris-primary/25 rotate-45 pointer-events-none"></div>
        <div class="absolute top-32 left-12 w-4 h-4 bg-ris-primary/20 rounded-full pointer-events-none"></div>
        <div
            class="absolute top-1/2 left-6 w-px h-32 bg-linear-to-b from-transparent via-ris-primary/25 to-transparent pointer-events-none">
        </div>
        <div class="absolute top-16 right-6 w-px h-24 bg-linear-to-b from-ris-accent/40 to-transparent pointer-events-none">
        </div>
        <div class="absolute top-64 right-10 w-8 h-8 border border-ris-accent/40 rounded-md rotate-12 pointer-events-none">
        </div>
        <div class="absolute bottom-40 left-10 w-3 h-3 bg-ris-accent/30 rotate-45 pointer-events-none"></div>
        <div class="absolute bottom-20 right-8 w-12 h-12 border-2 border-ris-primary/15 rounded-full pointer-events-none">
        </div>
        <div
            class="absolute bottom-8 right-20 w-px h-16 bg-linear-to-b from-ris-primary/25 to-transparent pointer-events-none">
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="text-center max-w-2xl mx-auto mb-10">
                <span class="text-ris-primary font-heading font-semibold text-sm uppercase tracking-wider">ভর্তি
                    ফরম</span>
                <h2 class="mt-3 font-heading font-bold text-2xl sm:text-3xl text-ris-dark">প্রাথমিক আবেদন ফরম পূরণ করুন</h2>
                <p class="mt-3 text-gray-500">নিচের ফরমটি সঠিকভাবে পূরণ করে জমা দিন। প্রয়োজনীয় জায়গায় সঠিক তথ্য
                    দিন।</p>
            </div>

            <div>
                <div class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg overflow-hidden border border-gray-300">

                    {{-- Header --}}
                    <div class="flex text-white">
                        <div class="w-1/2 bg-white text-gray-800 px-4 py-3 flex items-center gap-3">
                            <div class="w-20 h-20 rounded-full flex items-center justify-center shrink-0">
                                <img src="{{ asset('logo.png') }}" alt="Resma International School"
                                    class="h-14 w-auto object-contain">
                            </div>
                            <div>
                                <h1 class="text-lg md:text-xl font-bold tracking-wide text-pink-700">RESMA INTERNATIONAL
                                    SCHOOL</h1>
                                <p class="text-xs text-gray-500">প্রি-প্রাইমারি / প্রাইমারি স্কুল</p>
                            </div>
                        </div>
                        <div
                            class="w-1/2 bg-pink-700 px-4 py-3 text-right text-xs md:text-sm flex items-center justify-end">
                            <div>

                            </div>
                        </div>
                    </div>

                    <form id="admissionForm" action="{{ route('admission.store') }}" method="POST"
                        enctype="multipart/form-data" novalidate>
                        @csrf

                        {{-- Form Title --}}
                        <div class="bg-pink-100 px-4 py-2 flex flex-wrap items-center justify-between gap-2 border-b">
                            <h2 class="text-lg font-bold text-pink-800">প্রাথমিক আবেদন ফরম</h2>
                            <div class="text-sm">
                                শিক্ষাবর্ষ-২০ <span class="text-red-600">*</span>
                                <input type="text" name="academic_year"
                                    value="{{ old('academic_year', $defaultAcademicYear ?? '') }}" class="form-input w-16"
                                    maxlength="2">
                                @error('academic_year')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- AJAX result message --}}
                        <div id="admissionResult" class="px-4 py-3 text-sm rounded-lg border hidden"></div>

                        {{-- Form Body --}}
                        <div class="p-4 space-y-5">

                            {{-- Notice --}}
                            <p
                                class="text-lg text-center text-ris-primary bg-yellow-50 border border-yellow-200 rounded p-2">
                                ছাত্র/ছাত্রীর নাম, পিতা-মাতার নাম এবং জন্ম তারিখ, জন্ম নিবন্ধন সার্টিফিকেট অনুযায়ী সঠিকভাবে
                                পূরণ করতে হবে।
                            </p>

                            {{-- Student & Parent Details --}}
                            <div>
                                <div class="section-title">ছাত্র/ছাত্রীর ও পিতা-মাতার বিবরণ</div>
                                <div class="border border-t-0 border-gray-300 p-4 space-y-4 text-base">

                                    {{-- 0. Class --}}
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div class="md:col-span-2">
                                            <label class="font-medium">কোন শ্রেণিতে ভর্তি হতে চান? <span
                                                    class="text-red-600">*</span></label>
                                            <select name="class_level[]" class="form-input">
                                                <option value="">-- শ্রেণি নির্বাচন করুন --</option>
                                                @foreach ($classes as $class)
                                                    <option value="{{ $class->name }}"
                                                        {{ collect(old('class_level', []))->contains($class->name) ? 'selected' : '' }}>
                                                        {{ $class->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('class_level')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                            @error('class_level.0')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- 1. Student Name --}}
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label class="font-medium">১। ছাত্র/ছাত্রীর নাম (বাংলায়) <span
                                                    class="text-red-600">*</span></label>
                                            <input type="text" name="student_name_bn"
                                                value="{{ old('student_name_bn') }}" class="form-input">
                                            @error('student_name_bn')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="font-medium">ইংরেজিতে বড় অক্ষরে <span
                                                    class="text-red-600">*</span></label>
                                            <input type="text" name="student_name_en"
                                                value="{{ old('student_name_en') }}" class="form-input uppercase">
                                            @error('student_name_en')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- 2-5 --}}
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                        <div>
                                            <label class="font-medium">২। জন্ম তারিখ ও বয়স <span
                                                    class="text-red-600">*</span></label>
                                            <input type="text" data-date-mask name="dob" value="{{ old('dob') }}"
                                                class="form-input">
                                            @error('dob')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                            <input type="text" name="age" value="{{ old('age') }}"
                                                placeholder="বয়স *" class="form-input mt-1">
                                            @error('age')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="font-medium">৩। জাতীয়তা <span
                                                    class="text-red-600">*</span></label>
                                            <input type="text" name="nationality"
                                                value="{{ old('nationality', 'বাংলাদেশী') }}" class="form-input">
                                            @error('nationality')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="font-medium">৪। ধর্ম <span class="text-red-600">*</span></label>
                                            <input type="text" name="religion" value="{{ old('religion') }}"
                                                class="form-input">
                                            @error('religion')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="font-medium">৫। ব্লাড গ্রুপ <span
                                                    class="text-red-600">*</span></label>
                                            <input type="text" name="blood_group" value="{{ old('blood_group') }}"
                                                class="form-input">
                                            @error('blood_group')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- 6. Father --}}
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label class="font-medium">৬। পিতার নাম (বাংলায়) <span
                                                    class="text-red-600">*</span></label>
                                            <input type="text" name="father_name_bn"
                                                value="{{ old('father_name_bn') }}" class="form-input">
                                            @error('father_name_bn')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="font-medium">ইংরেজিতে (বড় অক্ষরে) <span
                                                    class="text-red-600">*</span></label>
                                            <input type="text" name="father_name_en"
                                                value="{{ old('father_name_en') }}" class="form-input uppercase">
                                            @error('father_name_en')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div>
                                        <label class="font-medium">পিতার পেশা ও পদবী <span
                                                class="text-red-600">*</span></label>
                                        <input type="text" name="father_occupation"
                                            value="{{ old('father_occupation') }}" class="form-input">
                                        @error('father_occupation')
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- 7. Mother --}}
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label class="font-medium">৭। মাতার নাম (বাংলায়) <span
                                                    class="text-red-600">*</span></label>
                                            <input type="text" name="mother_name_bn"
                                                value="{{ old('mother_name_bn') }}" class="form-input">
                                            @error('mother_name_bn')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="font-medium">ইংরেজিতে (বড় অক্ষরে) <span
                                                    class="text-red-600">*</span></label>
                                            <input type="text" name="mother_name_en"
                                                value="{{ old('mother_name_en') }}" class="form-input uppercase">
                                            @error('mother_name_en')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div>
                                        <label class="font-medium">মাতার পেশা ও পদবী <span
                                                class="text-red-600">*</span></label>
                                        <input type="text" name="mother_occupation"
                                            value="{{ old('mother_occupation') }}" class="form-input">
                                        @error('mother_occupation')
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Contact --}}
                            <div>
                                <div class="section-title">যোগাযোগ</div>
                                <div class="border border-t-0 border-gray-300 p-4 space-y-4 text-base">
                                    <div>
                                        <label class="font-medium">৮। বর্তমান ঠিকানা <span
                                                class="text-red-600">*</span></label>
                                        <textarea name="present_address" rows="2" class="form-input resize-none">{{ old('present_address') }}</textarea>
                                        @error('present_address')
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="font-medium">৯। স্থায়ী ঠিকানা <span
                                                class="text-red-600">*</span></label>
                                        <textarea name="permanent_address" rows="2" class="form-input resize-none">{{ old('permanent_address') }}</textarea>
                                        @error('permanent_address')
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label class="font-medium">১০। ফোন/মোবাইল <span
                                                    class="text-red-600">*</span></label>
                                            <input type="text" name="phone" value="{{ old('phone') }}"
                                                class="form-input">
                                            @error('phone')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="font-medium">ই-মেইল</label>
                                            <input type="email" name="email" value="{{ old('email') }}"
                                                class="form-input">
                                            @error('email')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div>
                                        <label class="font-medium">জরুরি প্রয়োজনে হলে <span
                                                class="text-red-600">*</span></label>
                                        <input type="text" name="emergency_contact"
                                            value="{{ old('emergency_contact') }}" class="form-input">
                                        @error('emergency_contact')
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Guardian Details --}}
                            <div>
                                <div class="section-title">অভিভাবকের বিবরণ</div>
                                <div class="border border-t-0 border-gray-300 p-4 space-y-4 text-base">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label class="font-medium">১১। আইনানুগ অভিভাবকের নাম <span
                                                    class="text-red-600">*</span><br><span
                                                    class="text-xs font-normal">(পিতা-মাতার অবর্তমানে)</span></label>
                                            <input type="text" name="legal_guardian_name"
                                                value="{{ old('legal_guardian_name') }}" class="form-input">
                                            @error('legal_guardian_name')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="font-medium">আইনানুগ অভিভাবকের পেশা <span
                                                    class="text-red-600">*</span></label>
                                            <input type="text" name="legal_guardian_occupation"
                                                value="{{ old('legal_guardian_occupation') }}" class="form-input">
                                            @error('legal_guardian_occupation')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label class="font-medium">ছাত্র/ছাত্রীর সাথে সম্পর্ক <span
                                                    class="text-red-600">*</span></label>
                                            <input type="text" name="legal_guardian_relation"
                                                value="{{ old('legal_guardian_relation') }}" class="form-input">
                                            @error('legal_guardian_relation')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="font-medium">ঠিকানা <span class="text-red-600">*</span></label>
                                            <input type="text" name="legal_guardian_address"
                                                value="{{ old('legal_guardian_address') }}" class="form-input">
                                            @error('legal_guardian_address')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <hr class="my-2 border-gray-300">

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label class="font-medium">স্থানীয় অভিভাবকের নাম <span
                                                    class="text-red-600">*</span></label>
                                            <input type="text" name="local_guardian_name"
                                                value="{{ old('local_guardian_name') }}" class="form-input">
                                            @error('local_guardian_name')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="font-medium">স্থানীয় অভিভাবকের পেশা <span
                                                    class="text-red-600">*</span></label>
                                            <input type="text" name="local_guardian_occupation"
                                                value="{{ old('local_guardian_occupation') }}" class="form-input">
                                            @error('local_guardian_occupation')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label class="font-medium">ছাত্র/ছাত্রীর সাথে অভিভাবকের সম্পর্ক <span
                                                    class="text-red-600">*</span></label>
                                            <input type="text" name="local_guardian_relation"
                                                value="{{ old('local_guardian_relation') }}" class="form-input">
                                            @error('local_guardian_relation')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="font-medium">ঠিকানা <span class="text-red-600">*</span></label>
                                            <input type="text" name="local_guardian_address"
                                                value="{{ old('local_guardian_address') }}" class="form-input">
                                            @error('local_guardian_address')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div>
                                        <label class="font-medium">ফোন/মোবাইল <span class="text-red-600">*</span></label>
                                        <input type="text" name="local_guardian_phone"
                                            value="{{ old('local_guardian_phone') }}" class="form-input">
                                        @error('local_guardian_phone')
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Previous School --}}
                            <div>
                                <div class="section-title">পূর্ববর্তী শ্রেণির বিবরণ</div>
                                <div class="border border-t-0 border-gray-300 p-4 space-y-4 text-base">
                                    <div>
                                        <label class="font-medium">ক) শিক্ষা প্রতিষ্ঠানের নাম </label>
                                        <input type="text" name="prev_school_name"
                                            value="{{ old('prev_school_name') }}" class="form-input">
                                        @error('prev_school_name')
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="font-medium">খ) প্রতিষ্ঠানের ঠিকানা </label>
                                        <input type="text" name="prev_school_address"
                                            value="{{ old('prev_school_address') }}" class="form-input">
                                        @error('prev_school_address')
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label class="font-medium">গ) শ্রেণির রোল নং</label>
                                            <input type="text" name="prev_roll_no" value="{{ old('prev_roll_no') }}"
                                                class="form-input">
                                        </div>
                                        <div>
                                            <label class="font-medium">বার্ষিক পরীক্ষার প্রাপ্ত নম্বর: </label>
                                            <input type="text" name="prev_marks" value="{{ old('prev_marks') }}"
                                                class="form-input">
                                            @error('prev_marks')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Reference --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-base pt-2">
                                <div>
                                    <label class="font-medium">রেফারেন্স <span class="text-red-600">*</span></label>
                                    <input type="text" name="reference" value="{{ old('reference') }}"
                                        class="form-input">
                                    @error('reference')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="font-medium">ফোন/মোবাইল <span class="text-red-600">*</span></label>
                                    <input type="text" name="reference_phone" value="{{ old('reference_phone') }}"
                                        class="form-input">
                                    @error('reference_phone')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Photo Upload --}}
                            <label for="student_photo"
                                class="block border-2 border-dashed border-pink-400 rounded-lg p-5 text-center cursor-pointer hover:bg-pink-50 transition-colors">
                                <span class="block text-base font-medium mb-2">শিক্ষার্থীর পাসপোর্ট সাইজের রঙিন ছবি আপলোড
                                    <span class="text-red-600">*</span>
                                </span>
                                <img id="photoPreview" alt="ছবি প্রিভিউ"
                                    class="hidden w-20 h-24 object-cover rounded-lg border border-gray-300 mx-auto mb-3">
                                <svg class="w-10 h-10 mx-auto mb-2 text-pink-400 pointer-events-none" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="block text-base text-gray-600 mb-1">ছবি আপলোড করতে ক্লিক করুন</span>
                                <span class="block text-sm text-gray-400">JPG, JPEG বা PNG (সর্বোচ্চ ২MB)</span>
                                <input type="file" id="student_photo" name="student_photo" accept="image/*"
                                    class="hidden">
                                @error('student_photo')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </label>

                            {{-- Submit --}}
                            <div class="pt-4 text-center">
                                <button type="submit" id="admissionSubmitBtn"
                                    class="bg-pink-700 hover:bg-pink-800 text-white font-semibold px-8 py-3.5 rounded shadow transition cursor-pointer">
                                    আবেদন জমা দিন
                                </button>
                            </div>
                        </div>
                    </form>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const form = document.getElementById('admissionForm');
                            if (!form) return;

                            const resultBox = document.getElementById('admissionResult');
                            const submitBtn = document.getElementById('admissionSubmitBtn');
                            const submitLabel = submitBtn.textContent;
                            let submitted = false;

                            const showResult = (type, text) => {
                                resultBox.classList.remove('hidden');
                                resultBox.className = 'px-4 py-3 text-sm rounded-lg border ' +
                                    (type === 'success' ?
                                        'bg-green-50 border-green-300 text-green-700' :
                                        'bg-red-50 border-red-300 text-red-700');
                                resultBox.textContent = text;
                            };

                            const pushToast = (type, message) => {
                                window.dispatchEvent(new CustomEvent('toast', {
                                    detail: {
                                        type,
                                        message
                                    }
                                }));
                            };

                            const clearErrors = () => {
                                form.querySelectorAll('[data-field-error]').forEach(el => el.remove());
                                form.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));
                            };

                            const showFieldError = (name, message) => {
                                const input = form.querySelector(`[name="${name}"]`);
                                if (!input) return;

                                input.classList.add('input-error');

                                let p = form.querySelector(`[data-field-error="${name}"]`);
                                if (!p) {
                                    p = document.createElement('p');
                                    p.setAttribute('data-field-error', name);
                                    p.className = 'mt-1 text-xs text-red-600';
                                    input.insertAdjacentElement('afterend', p);
                                }
                                p.textContent = message;
                            };

                            const requiredFields = {
                                'class_level[]': 'কোন শ্রেণিতে ভর্তি হতে চান তা নির্বাচন করুন।',
                                academic_year: 'শিক্ষাবর্ষ আবশ্যক।',
                                student_name_bn: 'ছাত্র/ছাত্রীর নাম (বাংলায়) আবশ্যক।',
                                student_name_en: 'ইংরেজিতে নাম আবশ্যক।',
                                dob: 'জন্ম তারিখ আবশ্যক।',
                                age: 'বয়স আবশ্যক।',
                                nationality: 'জাতীয়তা আবশ্যক।',
                                religion: 'ধর্ম আবশ্যক।',
                                blood_group: 'ব্লাড গ্রুপ আবশ্যক।',
                                father_name_bn: 'পিতার নাম (বাংলায়) আবশ্যক।',
                                father_name_en: 'পিতার নাম (ইংরেজি) আবশ্যক।',
                                father_occupation: 'পিতার পেশা আবশ্যক।',
                                mother_name_bn: 'মাতার নাম (বাংলায়) আবশ্যক।',
                                mother_name_en: 'মাতার নাম (ইংরেজি) আবশ্যক।',
                                mother_occupation: 'মাতার পেশা আবশ্যক।',
                                present_address: 'বর্তমান ঠিকানা আবশ্যক।',
                                permanent_address: 'স্থায়ী ঠিকানা আবশ্যক।',
                                phone: 'ফোন/মোবাইল আবশ্যক।',
                                emergency_contact: 'জরুরি প্রয়োজনে ফোন আবশ্যক।',
                                legal_guardian_name: 'আইনানুগ অভিভাবকের নাম আবশ্যক।',
                                legal_guardian_occupation: 'আইনানুগ অভিভাবকের পেশা আবশ্যক।',
                                legal_guardian_relation: 'আইনানুগ অভিভাবকের সম্পর্ক আবশ্যক।',
                                legal_guardian_address: 'আইনানুগ অভিভাবকের ঠিকানা আবশ্যক।',
                                local_guardian_name: 'স্থানীয় অভিভাবকের নাম আবশ্যক।',
                                local_guardian_occupation: 'স্থানীয় অভিভাবকের পেশা আবশ্যক।',
                                local_guardian_relation: 'স্থানীয় অভিভাবকের সম্পর্ক আবশ্যক।',
                                local_guardian_address: 'স্থানীয় অভিভাবকের ঠিকানা আবশ্যক।',
                                local_guardian_phone: 'স্থানীয় অভিভাবকের ফোন আবশ্যক।',
                                reference: 'রেফারেন্স আবশ্যক।',
                                reference_phone: 'রেফারেন্সের ফোন আবশ্যক।'
                            };

                            const validateFieldLocal = (name) => {
                                const input = form.querySelector(`[name="${name}"]`);
                                if (!input) return true;

                                const clearError = () => {
                                    input.classList.remove('input-error');
                                    const old = form.querySelector(`[data-field-error="${name}"]`);
                                    if (old) old.remove();
                                };

                                if (name === 'student_photo') {
                                    if (!form.querySelector('[name="student_photo"]').files.length) {
                                        showFieldError(name, 'শিক্ষার্থীর ছবি আবশ্যক।');
                                        return false;
                                    }
                                    clearError();
                                    return true;
                                }

                                if (name === 'email') {
                                    if (input.value.trim() && !/^\S+@\S+\.\S+$/.test(input.value.trim())) {
                                        showFieldError(name, 'সঠিক ইমেইল দিন।');
                                        return false;
                                    }
                                    clearError();
                                    return true;
                                }

                                if (!input.value.trim()) {
                                    showFieldError(name, requiredFields[name] || 'এই ঘরটি আবশ্যক।');
                                    return false;
                                }
                                clearError();
                                return true;
                            };

                            const attachValidation = (name) => {
                                const input = form.querySelector(`[name="${name}"]`);
                                if (input) input.addEventListener('blur', () => validateFieldLocal(name));
                            };

                            Object.keys(requiredFields).forEach(attachValidation);
                            attachValidation('email');
                            const firePhoto = form.querySelector('[name="student_photo"]');
                            if (firePhoto) {
                                firePhoto.addEventListener('change', (event) => {
                                    validateFieldLocal('student_photo');
                                    const preview = document.getElementById('photoPreview');
                                    if (!preview) return;
                                    const file = event.target.files && event.target.files[0];
                                    if (!file) {
                                        preview.classList.add('hidden');
                                        preview.removeAttribute('src');
                                        return;
                                    }
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        preview.src = e.target.result;
                                        preview.classList.remove('hidden');
                                    };
                                    reader.readAsDataURL(file);
                                });
                            }

                            form.addEventListener('submit', function(e) {
                                e.preventDefault();

                                if (submitBtn.disabled) return;

                                clearErrors();
                                resultBox.classList.add('hidden');

                                const allFields = [...Object.keys(requiredFields), 'email', 'student_photo'];
                                let valid = true;
                                allFields.forEach(name => {
                                    const ok = validateFieldLocal(name);
                                    if (ok) {
                                        const input = form.querySelector(`[name="${name}"]`);
                                        if (input) input.classList.remove('input-error');
                                    } else {
                                        valid = false;
                                    }
                                });

                                if (!valid) {
                                    const firstError = form.querySelector('[data-field-error]');
                                    if (firstError) {
                                        pushToast('error', firstError.textContent);
                                        firstError.scrollIntoView({
                                            behavior: 'smooth',
                                            block: 'center'
                                        });
                                    } else {
                                        pushToast('error', 'ফরমের প্রয়োজনীয় ঘরগুলো পূরণ করুন।');
                                    }
                                    return;
                                }

                                submitBtn.disabled = true;
                                submitBtn.textContent = 'জমা হচ্ছে...';

                                fetch(form.action, {
                                        method: 'POST',
                                        body: new FormData(form),
                                        headers: {
                                            'X-Requested-With': 'XMLHttpRequest',
                                            'Accept': 'application/json'
                                        },
                                    })
                                    .then(async response => {
                                        const data = await response.json();

                                        if (response.status === 422) {
                                            Object.entries(data.errors || {}).forEach(([name, messages]) => {
                                                showFieldError(name, messages[0]);
                                            });
                                            const firstError = form.querySelector('[data-field-error]');
                                            if (firstError) {
                                                pushToast('error', firstError.textContent);
                                                firstError.scrollIntoView({
                                                    behavior: 'smooth',
                                                    block: 'center'
                                                });
                                            } else {
                                                pushToast('error', data.message ||
                                                    'ফরম জমা দিতে সমস্যা হয়েছে। আবার চেষ্টা করুন।');
                                            }
                                            return;
                                        }

                                        if (response.ok) {
                                            submitted = true;
                                            submitBtn.textContent = 'আবেদন জমা হয়েছে';
                                            resultBox.classList.remove('hidden');
                                            resultBox.className =
                                                'px-4 py-3 text-sm rounded-lg border bg-green-50 border-green-300 text-green-700';
                                            resultBox.textContent = data.message ||
                                                'আপনার ভর্তি আবেদন সফলভাবে জমা হয়েছে।';
                                            pushToast('success', data.message ||
                                                'আপনার ভর্তি আবেদন সফলভাবে জমা হয়েছে।');
                                            form.reset();
                                            resultBox.scrollIntoView({
                                                behavior: 'smooth',
                                                block: 'center'
                                            });
                                            return;
                                        }

                                        showResult('error', data.message ||
                                            'আবেদন জমা দিতে সমস্যা হয়েছে। আবার চেষ্টা করুন।');
                                        pushToast('error', data.message ||
                                            'আবেদন জমা দিতে সমস্যা হয়েছে। আবার চেষ্টা করুন।');
                                    })
                                    .catch(() => {
                                        showResult('error', 'আবেদন জমা দিতে সমস্যা হয়েছে। আবার চেষ্টা করুন।');
                                        pushToast('error', 'আবেদন জমা দিতে সমস্যা হয়েছে। আবার চেষ্টা করুন।');
                                    })
                                    .finally(() => {
                                        // Left disabled after success so a stray
                                        // click cannot post the reset form again.
                                        if (submitted) return;
                                        submitBtn.disabled = false;
                                        submitBtn.textContent = submitLabel;
                                    });
                            });
                        });
                    </script>
                </div>
            </div>
        </div>
    </section>

    {{-- Admission Process --}}
    <section class="py-16 sm:py-20 bg-white section-pattern-grid relative overflow-hidden">
        <div class="absolute top-10 left-10 w-32 h-32 bg-ris-primary/5 rounded-full pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12 reveal">
                <span class="text-ris-primary font-heading font-semibold text-sm uppercase tracking-wider">ভর্তি
                    প্রক্রিয়া</span>
                <h2 class="mt-3 font-heading font-bold text-2xl sm:text-3xl text-ris-dark">কিভাবে ভর্তি হবেন</h2>
            </div>
            @php
                $steps = [
                    [
                        'num' => '০১',
                        'title' => 'ফরম সংগ্রহ',
                        'desc' => 'স্কুল ক্যাম্পাস থেকে বা অনলাইনে ভর্তি ফরম সংগ্রহ করুন।',
                    ],
                    [
                        'num' => '০২',
                        'title' => 'ফরম পূরণ',
                        'desc' => 'সঠিকভাবে ফরম পূরণ করে প্রয়োজনীয় কাগজপত্র সংযুক্ত করুন।',
                    ],
                    [
                        'num' => '০৩',
                        'title' => 'ভর্তি পরীক্ষা',
                        'desc' => 'নির্ধারিত সময়ে ভর্তি পরীক্ষায় অংশগ্রহণ করুন।',
                    ],
                    [
                        'num' => '০৪',
                        'title' => 'ফলাফল ও ভর্তি',
                        'desc' => 'পরীক্ষার ফলাফল ঘোষণার পর ভর্তি ফি পরিশোধ করে ভর্তি নিশ্চিত করুন।',
                    ],
                ];
            @endphp
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 reveal-stagger">
                @foreach ($steps as $step)
                    <div class="card p-6 text-center relative reveal">
                        <div class="w-14 h-14 mx-auto rounded-2xl gradient-logo flex items-center justify-center mb-4">
                            <span class="font-heading font-bold text-lg text-white">{{ $step['num'] }}</span>
                        </div>
                        <h3 class="font-heading font-bold text-ris-dark">{{ $step['title'] }}</h3>
                        <p class="mt-2 text-sm text-gray-500 leading-relaxed">{{ $step['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Requirements --}}
    <section class="py-16 sm:py-20 bg-ris-primary-50/40 section-pattern-diagonal relative overflow-hidden">
        <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-ris-accent/5 rounded-full pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12">
                <div class="reveal-left">
                    <span class="text-ris-primary font-heading font-semibold text-sm uppercase tracking-wider">প্রয়োজনীয়
                        কাগজপত্র</span>
                    <h2 class="mt-3 font-heading font-bold text-2xl text-ris-dark">ভর্তির জন্য প্রয়োজন</h2>
                    <ul class="mt-6 space-y-3">
                        @foreach (['জন্ম নিবন্ধন পত্রের ছবি', 'অভিভাবকের জাতীয় পরিচয়পত্রের ছবি', 'পূর্ববর্তী শিক্ষাপ্রতিষ্ঠানের সনদপত্র', '৪ কপি পাসপোর্ট সাইজ ছবি', 'ভর্তি ফরম (সঠিকভাবে পূরণ করা)'] as $req)
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-gray-600">{{ $req }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="reveal-right">
                    <span class="text-ris-primary font-heading font-semibold text-sm uppercase tracking-wider">ফি
                        কাঠামো</span>
                    <h2 class="mt-3 font-heading font-bold text-2xl text-ris-dark">বার্ষিক ফি</h2>
                    <div class="mt-6 space-y-4">
                        @php
                            $fees = [
                                ['class' => 'প্লে', 'amount' => '১০,০০০ টাকা'],
                                ['class' => 'নার্সারি', 'amount' => '১১,০০০ টাকা'],
                                ['class' => 'কেজি', 'amount' => '১১,০০০ টাকা'],
                                ['class' => 'প্রাথমিক (১ম-৫ম)', 'amount' => '১২,০০০ টাকা'],
                            ];
                        @endphp
                        @foreach ($fees as $fee)
                            <div class="card p-4 flex items-center justify-between">
                                <span class="font-heading font-medium text-ris-dark">{{ $fee['class'] }}</span>
                                <span class="badge">{{ $fee['amount'] }}</span>
                            </div>
                        @endforeach
                        <p class="text-sm text-gray-500 mt-4">* ফি কাঠামো পরিবর্তনযোগ্য। বিস্তারিত জানতে যোগাযোগ করুন।</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-16 sm:py-20 relative overflow-hidden">
        <div class="absolute inset-0 bg-linear-to-r from-ris-dark via-ris-accent to-ris-light"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal">
            <h2 class="font-heading font-bold text-3xl sm:text-4xl text-white">এখনই ভর্তি করুন</h2>
            <p class="mt-4 text-white/70 text-lg">আজই যোগাযোগ করুন বা স্কুল ক্যাম্পাসে আসুন</p>
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('contact') }}"
                    class="btn-primary bg-white text-ris-primary hover:bg-gray-100 shadow-lg shadow-black/20 px-8 py-3">যোগাযোগ
                    করুন</a>
            </div>
        </div>
    </section>

@endsection
