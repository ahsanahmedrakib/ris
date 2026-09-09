@extends('layouts.admin')

@section('title', 'ড্যাশবোর্ড')

@section('content')
    <div class="space-y-6">

        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-heading font-bold text-gray-900">ড্যাশবোর্ড</h1>
                <p class="text-sm text-gray-500 mt-1">রেশমা ইন্টারন্যাশনাল স্কুল প্রশাসনিক প্যানেল</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.students.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    নতুন ছাত্র যোগ করুন
                </a>
                <a href="{{ route('admin.attendance.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    উপস্থিতি নিন
                </a>
            </div>
        </div>

        {{-- Stat Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">

            {{-- মোট ছাত্র --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">মোট ছাত্র</p>
                        <p class="text-2xl font-heading font-bold text-gray-900 mt-1">
                            {{ number_format($totalStudents ?? 1250) }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 mt-3">
                    <span
                        class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 10l7-7m0 0l7 7m-7-7v18" />
                        </svg>
                        +12%
                    </span>
                    <span class="text-xs text-gray-400">গত মাস থেকে</span>
                </div>
            </div>

            {{-- মোট শিক্ষক --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">মোট শিক্ষক</p>
                        <p class="text-2xl font-heading font-bold text-gray-900 mt-1">
                            {{ number_format($totalTeachers ?? 68) }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 mt-3">
                    <span
                        class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 10l7-7m0 0l7 7m-7-7v18" />
                        </svg>
                        +5%
                    </span>
                    <span class="text-xs text-gray-400">গত মাস থেকে</span>
                </div>
            </div>

            {{-- মোট ক্লাস --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">মোট ক্লাস</p>
                        <p class="text-2xl font-heading font-bold text-gray-900 mt-1">
                            {{ number_format($totalClasses ?? 24) }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 mt-3">
                    <span
                        class="inline-flex items-center gap-1 text-xs font-medium text-gray-500 bg-gray-50 px-2 py-0.5 rounded-full">
                        স্থির
                    </span>
                    <span class="text-xs text-gray-400">কোনো পরিবর্তন নেই</span>
                </div>
            </div>

            {{-- মোট আয় --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">মোট আয়</p>
                        <p class="text-2xl font-heading font-bold text-gray-900 mt-1">
                            ৳{{ number_format($totalIncome ?? 4580000) }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-ris-primary/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-ris-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 mt-3">
                    <span
                        class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 10l7-7m0 0l7 7m-7-7v18" />
                        </svg>
                        +18%
                    </span>
                    <span class="text-xs text-gray-400">গত মাস থেকে</span>
                </div>
            </div>

        </div>

        {{-- Quick Actions --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-3">
            <a href="{{ route('admin.students.create') }}"
                class="flex flex-col items-center gap-2 p-4 bg-white rounded-xl border border-gray-200 hover:border-ris-primary hover:shadow-md transition-all text-center group">
                <div
                    class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center group-hover:bg-ris-primary/10 transition-colors">
                    <svg class="w-5 h-5 text-blue-600 group-hover:text-ris-primary transition-colors" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </div>
                <span class="text-xs font-medium text-gray-600">ছাত্র যোগ</span>
            </a>
            <a href="{{ route('admin.attendance.index') }}"
                class="flex flex-col items-center gap-2 p-4 bg-white rounded-xl border border-gray-200 hover:border-ris-primary hover:shadow-md transition-all text-center group">
                <div
                    class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center group-hover:bg-ris-primary/10 transition-colors">
                    <svg class="w-5 h-5 text-emerald-600 group-hover:text-ris-primary transition-colors" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <span class="text-xs font-medium text-gray-600">উপস্থিতি</span>
            </a>
            <a href="{{ route('admin.exams.create') }}"
                class="flex flex-col items-center gap-2 p-4 bg-white rounded-xl border border-gray-200 hover:border-ris-primary hover:shadow-md transition-all text-center group">
                <div
                    class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center group-hover:bg-ris-primary/10 transition-colors">
                    <svg class="w-5 h-5 text-amber-600 group-hover:text-ris-primary transition-colors" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <span class="text-xs font-medium text-gray-600">পরীক্ষা</span>
            </a>
            <a href="{{ route('admin.fees.index') }}"
                class="flex flex-col items-center gap-2 p-4 bg-white rounded-xl border border-gray-200 hover:border-ris-primary hover:shadow-md transition-all text-center group">
                <div
                    class="w-10 h-10 rounded-lg bg-ris-primary/10 flex items-center justify-center group-hover:bg-ris-primary/10 transition-colors">
                    <svg class="w-5 h-5 text-ris-primary group-hover:text-ris-primary transition-colors" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <span class="text-xs font-medium text-gray-600">ফি ব্যবস্থাপনা</span>
            </a>
            <a href="{{ route('admin.notices.create') }}"
                class="flex flex-col items-center gap-2 p-4 bg-white rounded-xl border border-gray-200 hover:border-ris-primary hover:shadow-md transition-all text-center group">
                <div
                    class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center group-hover:bg-ris-primary/10 transition-colors">
                    <svg class="w-5 h-5 text-purple-600 group-hover:text-ris-primary transition-colors" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                    </svg>
                </div>
                <span class="text-xs font-medium text-gray-600">নোটিশ দিন</span>
            </a>
            <a href="{{ route('admin.reports.index') }}"
                class="flex flex-col items-center gap-2 p-4 bg-white rounded-xl border border-gray-200 hover:border-ris-primary hover:shadow-md transition-all text-center group">
                <div
                    class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center group-hover:bg-ris-primary/10 transition-colors">
                    <svg class="w-5 h-5 text-gray-600 group-hover:text-ris-primary transition-colors" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <span class="text-xs font-medium text-gray-600">রিপোর্ট</span>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Recent Notices --}}
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <h2 class="font-heading font-semibold text-gray-900">সাম্প্রতিক নোটিশ</h2>
                    <a href="{{ route('admin.notices.index') }}"
                        class="text-sm text-ris-primary hover:text-ris-dark font-medium transition-colors">সব দেখুন</a>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse(($recentNotices ?? []) as $notice)
                        <div class="px-5 py-3.5 hover:bg-gray-50 transition-colors">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h3 class="text-sm font-medium text-gray-900 truncate">{{ $notice->title }}</h3>
                                    <p class="text-xs text-gray-500 mt-0.5 line-clamp-1">{{ $notice->description }}</p>
                                </div>
                                <span
                                    class="shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $notice->type === 'event' ? 'bg-blue-50 text-blue-700' : ($notice->type === 'holiday' ? 'bg-amber-50 text-amber-700' : 'bg-gray-100 text-gray-700') }}">
                                    {{ $notice->type === 'event' ? 'অনুষ্ঠান' : ($notice->type === 'holiday' ? 'ছুটি' : 'নোটিশ') }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">{{ $notice->created_at?->diffForHumans() ?? 'আজ' }}</p>
                        </div>
                    @empty
                        <div class="px-5 py-8 text-center">
                            <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                            </svg>
                            <p class="text-sm text-gray-400">কোনো নোটিশ নেই</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Fee Collection Summary --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="font-heading font-semibold text-gray-900">ফি সংগ্রহ সারসংক্ষেপ</h2>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <div class="flex items-center justify-between text-sm mb-1.5">
                            <span class="text-gray-600">সংগৃহীত</span>
                            <span
                                class="font-medium text-emerald-600">৳{{ number_format($feesCollected ?? 3200000) }}</span>
                        </div>
                        <div class="w-full h-2.5 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full"
                                style="width: {{ (($feesCollected ?? 3200000) / ($feesTotal ?? 4580000)) * 100 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between text-sm mb-1.5">
                            <span class="text-gray-600">বকেয়</span>
                            <span class="font-medium text-amber-600">৳{{ number_format($feesPending ?? 890000) }}</span>
                        </div>
                        <div class="w-full h-2.5 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-amber-500 rounded-full"
                                style="width: {{ (($feesPending ?? 890000) / ($feesTotal ?? 4580000)) * 100 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between text-sm mb-1.5">
                            <span class="text-gray-600">মোট</span>
                            <span class="font-medium text-gray-900">৳{{ number_format($feesTotal ?? 4580000) }}</span>
                        </div>
                    </div>
                    <hr class="border-gray-100">
                    <a href="{{ route('admin.fees.index') }}"
                        class="block text-center text-sm font-medium text-ris-primary hover:text-ris-dark transition-colors">ফি
                        ব্যবস্থাপনায় যান →</a>
                </div>
            </div>

        </div>

        {{-- Recent Admissions --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-heading font-semibold text-gray-900">সাম্প্রতিক ভর্তি</h2>
                <a href="{{ route('admin.students.index') }}"
                    class="text-sm text-ris-primary hover:text-ris-dark font-medium transition-colors">সব দেখুন</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left px-5 py-3 font-medium text-gray-500">ভর্তি নং</th>
                            <th class="text-left px-5 py-3 font-medium text-gray-500">নাম</th>
                            <th class="text-left px-5 py-3 font-medium text-gray-500">শ্রেণী</th>
                            <th class="text-left px-5 py-3 font-medium text-gray-500">অভিভাবক</th>
                            <th class="text-left px-5 py-3 font-medium text-gray-500">তারিখ</th>
                            <th class="text-left px-5 py-3 font-medium text-gray-500">অবস্থা</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse(($recentAdmissions ?? []) as $student)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3 font-medium text-gray-900">{{ $student->admission_no }}</td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-ris-primary/10 flex items-center justify-center text-ris-primary text-xs font-semibold">
                                            {{ substr($student->name_bn, 0, 1) }}
                                        </div>
                                        <span class="text-gray-900">{{ $student->name_bn }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-gray-600">{{ $student->class->name ?? '-' }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $student->guardian_name }}</td>
                                <td class="px-5 py-3 text-gray-500">{{ $student->created_at?->format('d/m/Y') ?? '-' }}
                                </td>
                                <td class="px-5 py-3">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">সক্রিয়</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-gray-400 text-sm">সাম্প্রতিক কোনো
                                    ভর্তি নেই</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
