@extends('layouts.admin')

@section('title', 'শ্রেণি রিপোর্ট')

@section('content')
    <div class="space-y-6">

        <div>
            <h1 class="text-2xl font-heading font-bold text-gray-900">শ্রেণি রিপোর্ট</h1>
            <p class="text-sm text-gray-500 mt-1">শ্রেণিভিত্তিক ছাত্র তালিকা, উপস্থিতি ও পরীক্ষার ফলাফল দেখুন</p>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <form method="GET">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">শ্রেণি নির্বাচন করুন</label>
                        <select name="class_id"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                            <option value="">শ্রেণি নির্বাচন করুন</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}"
                                    {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}
                                    ({{ $class->students_count ?? 0 }} জন)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit"
                            class="px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors">
                            রিপোর্ট দেখুন
                        </button>
                        <a href="{{ route('admin.reports.class') }}"
                            class="px-4 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                            রিসেট
                        </a>
                    </div>
                </div>
            </form>
        </div>

        @if (isset($class))
            {{-- Class Info --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl bg-ris-primary/10 flex items-center justify-center shrink-0">
                        <svg class="w-7 h-7 text-ris-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-heading font-bold text-gray-900">{{ $class->name }}</h2>
                        <p class="text-sm text-gray-500">{{ $class->section ?? 'সকল সেকশন' }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mt-5 pt-5 border-t border-gray-100">
                    <div>
                        <p class="text-xs text-gray-500">শ্রেণি</p>
                        <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $class->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">সেকশন</p>
                        <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $class->section ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">মোট ছাত্র</p>
                        <p class="text-sm font-medium text-gray-900 mt-0.5">
                            {{ $class->students_count ?? ($class->students->count() ?? 0) }} জন</p>
                    </div>
                </div>
            </div>

            {{-- Student List --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="font-heading font-semibold text-gray-900">শ্রেণির ছাত্র তালিকা</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="text-left px-5 py-3.5 font-medium text-gray-500">ক্রমিক</th>
                                <th class="text-left px-5 py-3.5 font-medium text-gray-500">ভর্তি নং</th>
                                <th class="text-left px-5 py-3.5 font-medium text-gray-500">নাম</th>
                                <th class="text-left px-5 py-3.5 font-medium text-gray-500">রোল নং</th>
                                <th class="text-center px-5 py-3.5 font-medium text-gray-500">মোট দিন</th>
                                <th class="text-center px-5 py-3.5 font-medium text-gray-500">উপস্থিত</th>
                                <th class="text-center px-5 py-3.5 font-medium text-gray-500">অনুপস্থিত</th>
                                <th class="text-center px-5 py-3.5 font-medium text-gray-500">উপস্থিতির হার</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse(($class->students ?? []) as $index => $student)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-5 py-3.5 text-gray-500">{{ $index + 1 }}</td>
                                    <td class="px-5 py-3.5 font-mono text-xs text-gray-500">{{ $student->admission_no }}
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-8 h-8 rounded-full bg-ris-primary/10 flex items-center justify-center text-ris-primary text-xs font-semibold shrink-0">
                                                {{ substr($student->name_bn, 0, 1) }}
                                            </div>
                                            <span class="font-medium text-gray-900">{{ $student->name_bn }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5 text-gray-600">{{ $student->roll_no }}</td>
                                    <td class="px-5 py-3.5 text-center text-gray-600">
                                        {{ $student->attendance_summary['total_days'] ?? '-' }}</td>
                                    <td class="px-5 py-3.5 text-center text-emerald-600 font-medium">
                                        {{ $student->attendance_summary['present'] ?? '-' }}</td>
                                    <td class="px-5 py-3.5 text-center text-red-600 font-medium">
                                        {{ $student->attendance_summary['absent'] ?? '-' }}</td>
                                    <td class="px-5 py-3.5 text-center">
                                        @php $pct = $student->attendance_summary['percentage'] ?? null @endphp
                                        @if ($pct !== null)
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                            {{ $pct >= 75 ? 'bg-emerald-50 text-emerald-700' : ($pct >= 50 ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700') }}">
                                                {{ $pct }}%
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-5 py-12 text-center">
                                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        <p class="text-gray-500 font-medium">এই শ্রেণিতে কোনো ছাত্র নেই</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Exam Results Grouped by Student --}}
            @if (isset($examResults) && $examResults->count())
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h2 class="font-heading font-semibold text-gray-900">পরীক্ষার ফলাফল (ছাত্র অনুযায়ী)</h2>
                    </div>
                    @php $byStudent = $examResults->groupBy('student_id') @endphp
                    <div class="divide-y divide-gray-100">
                        @foreach ($byStudent as $studentId => $results)
                            @php $first = $results->first() @endphp
                            <div>
                                <div class="px-5 py-3 bg-gray-50 flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full bg-ris-primary/10 flex items-center justify-center text-ris-primary text-xs font-semibold shrink-0">
                                        {{ substr($first->student->name_bn ?? '?', 0, 1) }}
                                    </div>
                                    <span
                                        class="font-medium text-gray-900 text-sm">{{ $first->student->name_bn ?? '-' }}</span>
                                    <span class="text-xs text-gray-400">রোল: {{ $first->student->roll_no ?? '-' }}</span>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr class="border-b border-gray-100">
                                                <th class="text-left px-5 py-2.5 font-medium text-gray-500">পরীক্ষা</th>
                                                <th class="text-left px-5 py-2.5 font-medium text-gray-500">বিষয়</th>
                                                <th class="text-center px-5 py-2.5 font-medium text-gray-500">পূর্ণমান</th>
                                                <th class="text-center px-5 py-2.5 font-medium text-gray-500">প্রাপ্তমান
                                                </th>
                                                <th class="text-center px-5 py-2.5 font-medium text-gray-500">গ্রেড</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-50">
                                            @foreach ($results as $result)
                                                <tr class="hover:bg-gray-50 transition-colors">
                                                    <td class="px-5 py-2.5 text-gray-600">{{ $result->exam->name ?? '-' }}
                                                    </td>
                                                    <td class="px-5 py-2.5 text-gray-900 font-medium">
                                                        {{ $result->subject->name ?? '-' }}</td>
                                                    <td class="px-5 py-2.5 text-center text-gray-600">
                                                        {{ $result->full_marks ?? '-' }}</td>
                                                    <td class="px-5 py-2.5 text-center text-gray-900 font-medium">
                                                        {{ $result->marks ?? '-' }}</td>
                                                    <td class="px-5 py-2.5 text-center">
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                        {{ ($result->grade ?? '') === 'A+' ? 'bg-emerald-50 text-emerald-700' : (($result->grade ?? '') === 'F' ? 'bg-red-50 text-red-700' : 'bg-blue-50 text-blue-700') }}">
                                                            {{ $result->grade ?? '-' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @else
            <div class="bg-white rounded-xl border border-gray-200 px-5 py-12 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <p class="text-gray-500 font-medium">রিপোর্ট দেখতে একটি শ্রেণি নির্বাচন করুন</p>
                <p class="text-sm text-gray-400 mt-1">উপরের ফিল্টার ব্যবহার করে শ্রেণি নির্বাচন করুন</p>
            </div>
        @endif

    </div>
@endsection
