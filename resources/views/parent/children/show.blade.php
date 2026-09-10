@extends('layouts.parent')

@section('title', ($child->name ?? 'সন্তান') . ' — অভিভাবক পোর্টাল')
@section('page-title', $child->name ?? 'সন্তানের বিস্তারিত')

@section('content')

    <div class="space-y-6">

        {{-- Back Link --}}
        <a href="{{ route('parent.children') }}"
            class="inline-flex items-center gap-1.5 text-sm font-medium text-ris-primary hover:text-ris-dark transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            সন্তান তালিকায় ফিরুন
        </a>

        {{-- Profile Header --}}
        <div class="card overflow-hidden">
            <div class="bg-linear-to-r from-ris-dark via-ris-primary to-ris-light p-6 sm:p-8 relative">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2">
                </div>
                <div class="absolute bottom-0 left-1/3 w-20 h-20 bg-white/5 rounded-full translate-y-1/2"></div>
                <div class="relative z-10 flex flex-col sm:flex-row items-center sm:items-start gap-5">
                    <div
                        class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-white/20 border-3 border-white/30 flex items-center justify-center text-white font-heading font-bold text-3xl sm:text-4xl shrink-0">
                        @if (isset($child->photo))
                            <img src="{{ asset('storage/' . $child->photo) }}" alt="{{ $child->name }}"
                                class="w-full h-full rounded-full object-cover">
                        @else
                            {{ substr($child->name ?? 'ছ', 0, 1) }}
                        @endif
                    </div>
                    <div class="text-center sm:text-left">
                        <h2 class="font-heading font-bold text-2xl sm:text-3xl text-white">{{ $child->name }}</h2>
                        <p class="text-white/70 mt-1">{{ $child->class->name ?? '' }} · {{ $child->section->name ?? '' }} ·
                            রোল: {{ $child->roll_no ?? '—' }}</p>
                        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mt-3">
                            <span class="bg-white/20 text-white text-xs font-medium px-3 py-1 rounded-full">ভর্তি নং:
                                {{ $child->admission_no ?? '—' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Cards --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Personal Info --}}
            <div class="card p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-lg bg-ris-primary/10 flex items-center justify-center">
                        <svg class="w-5 h-5 text-ris-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <h3 class="font-heading font-bold text-ris-dark">ব্যক্তিগত তথ্য</h3>
                </div>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between py-2 border-b border-gray-50">
                        <span class="text-ris-gray">পূর্ণ নাম</span>
                        <span class="font-medium text-gray-800">{{ $child->name ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-50">
                        <span class="text-ris-gray">জন্ম তারিখ</span>
                        <span
                            class="font-medium text-gray-800">{{ isset($child->date_of_birth) ? \Carbon\Carbon::parse($child->date_of_birth)->format('d M, Y') : '—' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-50">
                        <span class="text-ris-gray">রক্তের গ্রুপ</span>
                        <span class="font-medium text-gray-800">{{ $child->blood_group ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-50">
                        <span class="text-ris-gray">লিঙ্গ</span>
                        <span class="font-medium text-gray-800">{{ $child->gender ?? '—' }}</span>
                    </div>
                    <div class="py-2">
                        <span class="text-ris-gray block mb-1">ঠিকানা</span>
                        <span class="font-medium text-gray-800">{{ $child->address ?? '—' }}</span>
                    </div>
                </div>
            </div>

            {{-- Academic Info --}}
            <div class="card p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h3 class="font-heading font-bold text-ris-dark">শিক্ষাগত তথ্য</h3>
                </div>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between py-2 border-b border-gray-50">
                        <span class="text-ris-gray">শ্রেণি</span>
                        <span class="font-medium text-gray-800">{{ $child->class->name ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-50">
                        <span class="text-ris-gray">শাখা</span>
                        <span class="font-medium text-gray-800">{{ $child->section->name ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-50">
                        <span class="text-ris-gray">রোল নং</span>
                        <span class="font-medium text-gray-800">{{ $child->roll_no ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-50">
                        <span class="text-ris-gray">ভর্তি নং</span>
                        <span class="font-medium text-gray-800">{{ $child->admission_no ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-ris-gray">সেশন</span>
                        <span class="font-medium text-gray-800">{{ $child->session ?? '—' }}</span>
                    </div>
                </div>
            </div>

            {{-- Guardian Info --}}
            <div class="card p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="font-heading font-bold text-ris-dark">অভিভাবক তথ্য</h3>
                </div>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between py-2 border-b border-gray-50">
                        <span class="text-ris-gray">পিতার নাম</span>
                        <span class="font-medium text-gray-800">{{ $child->father_name ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-50">
                        <span class="text-ris-gray">মাতার নাম</span>
                        <span class="font-medium text-gray-800">{{ $child->mother_name ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-50">
                        <span class="text-ris-gray">অভিভাবক ফোন</span>
                        <span class="font-medium text-gray-800">{{ $child->guardian_phone ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-ris-gray">জরুরি যোগাযোগ</span>
                        <span class="font-medium text-gray-800">{{ $child->emergency_phone ?? '—' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Attendance Summary --}}
        <div class="card p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <h3 class="font-heading font-bold text-ris-dark">মাসিক উপস্থিতি সারসংক্ষেপ</h3>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-5">
                <div class="text-center p-3 bg-gray-50 rounded-xl">
                    <p class="text-2xl font-heading font-bold text-ris-dark">{{ $attendance_summary['total_days'] ?? 0 }}
                    </p>
                    <p class="text-xs text-ris-gray mt-1">মোট দিন</p>
                </div>
                <div class="text-center p-3 bg-emerald-50 rounded-xl">
                    <p class="text-2xl font-heading font-bold text-emerald-600">{{ $attendance_summary['present'] ?? 0 }}
                    </p>
                    <p class="text-xs text-ris-gray mt-1">উপস্থিত</p>
                </div>
                <div class="text-center p-3 bg-red-50 rounded-xl">
                    <p class="text-2xl font-heading font-bold text-red-600">{{ $attendance_summary['absent'] ?? 0 }}</p>
                    <p class="text-xs text-ris-gray mt-1">অনুপস্থিত</p>
                </div>
                <div class="text-center p-3 bg-amber-50 rounded-xl">
                    <p class="text-2xl font-heading font-bold text-amber-600">{{ $attendance_summary['late'] ?? 0 }}</p>
                    <p class="text-xs text-ris-gray mt-1">বিলম্বিত</p>
                </div>
                <div class="text-center p-3 bg-ris-primary/5 rounded-xl">
                    <p class="text-2xl font-heading font-bold text-ris-primary">
                        {{ $attendance_summary['percentage'] ?? 0 }}%</p>
                    <p class="text-xs text-ris-gray mt-1">উপস্থিতির হার</p>
                </div>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('parent.attendance', ['child_id' => $child->id]) }}"
                    class="text-sm font-medium text-ris-primary hover:text-ris-dark transition-colors">বিস্তারিত দেখুন
                    →</a>
            </div>
        </div>

        {{-- Recent Exam Results --}}
        <div class="card p-6">
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-ris-primary/10 flex items-center justify-center">
                        <svg class="w-5 h-5 text-ris-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="font-heading font-bold text-ris-dark">সর্বশেষ পরীক্ষার ফলাফল</h3>
                </div>
                <a href="{{ route('parent.exams', ['child_id' => $child->id]) }}"
                    class="text-sm font-medium text-ris-primary hover:text-ris-dark transition-colors">সব দেখুন →</a>
            </div>

            @if (!empty($recent_results) && count($recent_results) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th
                                    class="text-left py-3 px-4 font-heading font-semibold text-ris-dark text-xs uppercase tracking-wider">
                                    বিষয়</th>
                                <th
                                    class="text-center py-3 px-4 font-heading font-semibold text-ris-dark text-xs uppercase tracking-wider">
                                    পূর্ণমাণ</th>
                                <th
                                    class="text-center py-3 px-4 font-heading font-semibold text-ris-dark text-xs uppercase tracking-wider">
                                    প্রাপ্ত</th>
                                <th
                                    class="text-center py-3 px-4 font-heading font-semibold text-ris-dark text-xs uppercase tracking-wider">
                                    গ্রেড</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recent_results as $result)
                                <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                                    <td class="py-3 px-4 text-gray-800 font-medium">
                                        {{ $result->subject->name ?? ($result->subject_name ?? '—') }}</td>
                                    <td class="py-3 px-4 text-center text-gray-600">{{ $result->full_marks ?? '—' }}</td>
                                    <td class="py-3 px-4 text-center font-medium text-gray-800">
                                        {{ $result->marks ?? '—' }}</td>
                                    <td class="py-3 px-4 text-center">
                                        @if (isset($result->grade))
                                            <span
                                                class="badge {{ ($result->grade ?? '') === 'F' ? 'badge-danger' : 'badge-success' }} text-xs">{{ $result->grade }}</span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center text-ris-gray py-6 text-sm">কোনো পরীক্ষার ফলাফল পাওয়া যায়নি।</p>
            @endif
        </div>

        {{-- Fee Status --}}
        <div class="card p-6">
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="font-heading font-bold text-ris-dark">ফি অবস্থা</h3>
                </div>
                <a href="{{ route('parent.fees', ['child_id' => $child->id]) }}"
                    class="text-sm font-medium text-ris-primary hover:text-ris-dark transition-colors">বিস্তারিত দেখুন
                    →</a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="p-4 bg-gray-50 rounded-xl text-center">
                    <p class="text-xs text-ris-gray mb-1">মোট বকেয়া</p>
                    <p class="text-xl font-heading font-bold text-ris-light">
                        ৳{{ number_format($fee_status['total_due'] ?? 0, 2) }}</p>
                </div>
                <div class="p-4 bg-emerald-50 rounded-xl text-center">
                    <p class="text-xs text-ris-gray mb-1">পরিশোধিত</p>
                    <p class="text-xl font-heading font-bold text-emerald-600">
                        ৳{{ number_format($fee_status['total_paid'] ?? 0, 2) }}</p>
                </div>
                <div class="p-4 bg-amber-50 rounded-xl text-center">
                    <p class="text-xs text-ris-gray mb-1">অপেক্ষমাণ</p>
                    <p class="text-xl font-heading font-bold text-amber-600">
                        ৳{{ number_format($fee_status['total_pending'] ?? 0, 2) }}</p>
                </div>
            </div>
        </div>

        {{-- Library & Transport --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Library --}}
            <div class="card p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h3 class="font-heading font-bold text-ris-dark">লাইব্রেরি বই ধার</h3>
                </div>

                @if (!empty($library_books) && count($library_books) > 0)
                    <div class="space-y-3">
                        @foreach ($library_books->take(5) as $book)
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                <div class="w-8 h-8 rounded bg-blue-100 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 truncate">{{ $book->title ?? '—' }}</p>
                                    <p class="text-xs text-ris-gray">ফেরার তারিখ:
                                        {{ isset($book->return_date) ? \Carbon\Carbon::parse($book->return_date)->format('d M, Y') : '—' }}
                                    </p>
                                </div>
                                @if (isset($book->is_returned) && !$book->is_returned)
                                    <span class="badge-warning badge text-[10px]">ফেরানো হয়নি</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-ris-gray py-4 text-sm">কোনো বই ধার নেই।</p>
                @endif
            </div>

            {{-- Transport --}}
            <div class="card p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7h8m0 0v8m0-8l-4 4m4-4l-4 4" />
                        </svg>
                    </div>
                    <h3 class="font-heading font-bold text-ris-dark">পরিবহন তথ্য</h3>
                </div>

                @if (isset($transport_info))
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between py-2 border-b border-gray-50">
                            <span class="text-ris-gray">রুট</span>
                            <span class="font-medium text-gray-800">{{ $transport_info->route ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-50">
                            <span class="text-ris-gray">বাস নং</span>
                            <span class="font-medium text-gray-800">{{ $transport_info->bus_number ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-50">
                            <span class="text-ris-gray">পিকআপ সময়</span>
                            <span class="font-medium text-gray-800">{{ $transport_info->pickup_time ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-ris-gray">ড্রপ সময়</span>
                            <span class="font-medium text-gray-800">{{ $transport_info->drop_time ?? '—' }}</span>
                        </div>
                    </div>
                @else
                    <p class="text-center text-ris-gray py-4 text-sm">পরিবহন সুবিধা নেই।</p>
                @endif
            </div>

        </div>

    </div>

@endsection
