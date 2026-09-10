@extends('layouts.admin')

@section('title', 'রিপোর্ট ড্যাশবোর্ড')

@section('content')
    <div class="space-y-6">

        <div>
            <h1 class="text-2xl font-heading font-bold text-gray-900">রিপোর্ট ড্যাশবোর্ড</h1>
            <p class="text-sm text-gray-500 mt-1">বিভিন্ন ধরনের রিপোর্ট দেখুন ও ডাউনলোড করুন</p>
        </div>

        {{-- Report Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">

            {{-- Student Reports --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition-shadow group">
                <div class="p-5">
                    <div
                        class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center mb-4 group-hover:bg-ris-primary/10 transition-colors">
                        <svg class="w-6 h-6 text-blue-600 group-hover:text-ris-primary transition-colors" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h3 class="font-heading font-semibold text-gray-900 mb-1">ছাত্র রিপোর্ট</h3>
                    <p class="text-sm text-gray-500 mb-4">ছাত্র সংখ্যা, ভর্তি, স্থানান্তরের রিপোর্ট</p>
                    <div class="space-y-2">
                        <a href="{{ route('admin.reports.students') }}"
                            class="flex items-center gap-2 text-sm text-gray-600 hover:text-ris-primary transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            ছাত্র তালিকা
                        </a>
                        <a href="#"
                            class="flex items-center gap-2 text-sm text-gray-600 hover:text-ris-primary transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            শ্রেণি অনুযায়ী ছাত্র
                        </a>
                        <a href="#"
                            class="flex items-center gap-2 text-sm text-gray-600 hover:text-ris-primary transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            নতুন ভর্তি রিপোর্ট
                        </a>
                    </div>
                </div>
            </div>

            {{-- Attendance Reports --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition-shadow group">
                <div class="p-5">
                    <div
                        class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center mb-4 group-hover:bg-ris-primary/10 transition-colors">
                        <svg class="w-6 h-6 text-emerald-600 group-hover:text-ris-primary transition-colors" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <h3 class="font-heading font-semibold text-gray-900 mb-1">উপস্থিতি রিপোর্ট</h3>
                    <p class="text-sm text-gray-500 mb-4">দৈনিক, সাপ্তাহিক, মাসিক উপস্থিতি রিপোর্ট</p>
                    <div class="space-y-2">
                        <a href="{{ route('admin.reports.attendance') }}"
                            class="flex items-center gap-2 text-sm text-gray-600 hover:text-ris-primary transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            দৈনিক উপস্থিতি
                        </a>
                        <a href="#"
                            class="flex items-center gap-2 text-sm text-gray-600 hover:text-ris-primary transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            মাসিক সারসংক্ষেপ
                        </a>
                        <a href="#"
                            class="flex items-center gap-2 text-sm text-gray-600 hover:text-ris-primary transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            অনুপস্থিতি রিপোর্ট
                        </a>
                    </div>
                </div>
            </div>

            {{-- Exam Reports --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition-shadow group">
                <div class="p-5">
                    <div
                        class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center mb-4 group-hover:bg-ris-primary/10 transition-colors">
                        <svg class="w-6 h-6 text-amber-600 group-hover:text-ris-primary transition-colors" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="font-heading font-semibold text-gray-900 mb-1">পরীক্ষার ফলাফল</h3>
                    <p class="text-sm text-gray-500 mb-4">পরীক্ষার ফলাফল, গ্রেড বিতরণের রিপোর্ট</p>
                    <div class="space-y-2">
                        <a href="{{ route('admin.reports.exams') }}"
                            class="flex items-center gap-2 text-sm text-gray-600 hover:text-ris-primary transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            পরীক্ষার সারসংক্ষেপ
                        </a>
                        <a href="#"
                            class="flex items-center gap-2 text-sm text-gray-600 hover:text-ris-primary transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            গ্রেড বিতরণ
                        </a>
                        <a href="#"
                            class="flex items-center gap-2 text-sm text-gray-600 hover:text-ris-primary transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            বিষয়ভিত্তিক ফলাফল
                        </a>
                    </div>
                </div>
            </div>

            {{-- Fee Reports --}}
            <div
                class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition-shadow group">
                <div class="p-5">
                    <div
                        class="w-12 h-12 rounded-xl bg-ris-primary/10 flex items-center justify-center mb-4 group-hover:bg-ris-primary/10 transition-colors">
                        <svg class="w-6 h-6 text-ris-primary group-hover:text-ris-primary transition-colors"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="font-heading font-semibold text-gray-900 mb-1">আর্থিক রিপোর্ট</h3>
                    <p class="text-sm text-gray-500 mb-4">ফি সংগ্রহ, বকেয়, মোট আয়ের রিপোর্ট</p>
                    <div class="space-y-2">
                        <a href="{{ route('admin.reports.fees') }}"
                            class="flex items-center gap-2 text-sm text-gray-600 hover:text-ris-primary transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            ফি সংগ্রহ রিপোর্ট
                        </a>
                        <a href="#"
                            class="flex items-center gap-2 text-sm text-gray-600 hover:text-ris-primary transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            বকেয় রিপোর্ট
                        </a>
                        <a href="#"
                            class="flex items-center gap-2 text-sm text-gray-600 hover:text-ris-primary transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            মাসিক আয়-ব্যয়
                        </a>
                    </div>
                </div>
            </div>

            {{-- Staff Reports --}}
            <div
                class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition-shadow group">
                <div class="p-5">
                    <div
                        class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center mb-4 group-hover:bg-ris-primary/10 transition-colors">
                        <svg class="w-6 h-6 text-purple-600 group-hover:text-ris-primary transition-colors" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h3 class="font-heading font-semibold text-gray-900 mb-1">কর্মচারী রিপোর্ট</h3>
                    <p class="text-sm text-gray-500 mb-4">কর্মচারী তালিকা, বেতন রিপোর্ট</p>
                    <div class="space-y-2">
                        <a href="{{ route('admin.reports.staff') }}"
                            class="flex items-center gap-2 text-sm text-gray-600 hover:text-ris-primary transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            কর্মচারী তালিকা
                        </a>
                        <a href="#"
                            class="flex items-center gap-2 text-sm text-gray-600 hover:text-ris-primary transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            বেতন রিপোর্ট
                        </a>
                        <a href="#"
                            class="flex items-center gap-2 text-sm text-gray-600 hover:text-ris-primary transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            বিভাগ অনুযায়ী
                        </a>
                    </div>
                </div>
            </div>

            {{-- Transport Reports --}}
            <div
                class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition-shadow group">
                <div class="p-5">
                    <div
                        class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center mb-4 group-hover:bg-ris-primary/10 transition-colors">
                        <svg class="w-6 h-6 text-gray-600 group-hover:text-ris-primary transition-colors" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7h8m0 0v8m0-8l-4 4m4-4l-4 4" />
                        </svg>
                    </div>
                    <h3 class="font-heading font-semibold text-gray-900 mb-1">পরিবহন রিপোর্ট</h3>
                    <p class="text-sm text-gray-500 mb-4">বাস, রুট, ছাত্র বিতরণের রিপোর্ট</p>
                    <div class="space-y-2">
                        <a href="{{ route('admin.reports.transport') }}"
                            class="flex items-center gap-2 text-sm text-gray-600 hover:text-ris-primary transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            বাস তালিকা
                        </a>
                        <a href="#"
                            class="flex items-center gap-2 text-sm text-gray-600 hover:text-ris-primary transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            রুট বিশ্লেষণ
                        </a>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection
