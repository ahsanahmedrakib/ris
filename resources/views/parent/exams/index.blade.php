@extends('layouts.parent')

@section('title', 'পরীক্ষার ফলাফল — অভিভাবক পোর্টাল')
@section('page-title', 'পরীক্ষার ফলাফল')

@section('content')

<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-heading font-bold text-xl text-ris-dark">পরীক্ষার ফলাফল</h2>
            <p class="text-sm text-ris-gray mt-1">সন্তানের পরীক্ষার ফলাফল দেখুন</p>
        </div>
        @if(isset($results) && count($results) > 0)
            <button onclick="window.print()" class="btn-secondary text-sm py-2 px-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                রিপোর্ট কার্ড প্রিন্ট
            </button>
        @endif
    </div>

    {{-- Filters --}}
    <div class="card p-4 sm:p-5">
        <form method="GET" action="{{ route('parent.exams') }}" class="flex flex-col sm:flex-row items-stretch sm:items-end gap-4">
            {{-- Child Selector --}}
            @if(!empty($children) && count($children) > 1)
                <div class="flex-1">
                    <label class="block text-xs font-medium text-ris-gray mb-1.5">সন্তান নির্বাচন করুন</label>
                    <select name="child_id" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-ris-primary/30 focus:border-ris-primary transition-all">
                        @foreach($children as $child)
                            <option value="{{ $child->id }}" {{ (request('child_id', $selected_child_id ?? null) == $child->id) ? 'selected' : '' }}>
                                {{ $child->name }} — {{ $child->class->name ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- Exam Selector --}}
            <div class="flex-1">
                <label class="block text-xs font-medium text-ris-gray mb-1.5">পরীক্ষা নির্বাচন করুন</label>
                <select name="exam_id" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-ris-primary/30 focus:border-ris-primary transition-all">
                    <option value="">সব পরীক্ষা</option>
                    @if(!empty($exams))
                        @foreach($exams as $exam)
                            <option value="{{ $exam->id }}" {{ (request('exam_id') == $exam->id) ? 'selected' : '' }}>
                                {{ $exam->name }} {{ isset($exam->year) ? '(' . $exam->year . ')' : '' }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <button type="submit" class="btn-primary py-2.5 px-5 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                খুঁজুন
            </button>
        </form>
    </div>

    {{-- GPA Summary --}}
    @if(isset($gpa_summary))
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="card p-5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-ris-primary/10 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-ris-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-ris-gray">GPA</p>
                        <p class="text-2xl font-heading font-bold text-ris-primary">{{ $gpa_summary['gpa'] ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <div class="card p-5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-ris-gray">মোট নম্বর</p>
                        <p class="text-2xl font-heading font-bold text-emerald-600">{{ $gpa_summary['total_marks'] ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <div class="card p-5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-ris-gray">গড়</p>
                        <p class="text-2xl font-heading font-bold text-amber-600">{{ $gpa_summary['average'] ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <div class="card p-5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-ris-gray">মোট বিষয়</p>
                        <p class="text-2xl font-heading font-bold text-blue-600">{{ $gpa_summary['total_subjects'] ?? '—' }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Results Table --}}
    <div class="card overflow-hidden">
        <div class="p-5 border-b border-gray-100">
            <h3 class="font-heading font-bold text-ris-dark">বিষয়ভিত্তিক ফলাফল</h3>
        </div>

        @if(!empty($results) && count($results) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="text-left py-3 px-5 font-heading font-semibold text-ris-dark text-xs uppercase tracking-wider">বিষয়</th>
                            <th class="text-center py-3 px-5 font-heading font-semibold text-ris-dark text-xs uppercase tracking-wider">পূর্ণমাণ</th>
                            <th class="text-center py-3 px-5 font-heading font-semibold text-ris-dark text-xs uppercase tracking-wider">প্রাপ্ত নম্বর</th>
                            <th class="text-center py-3 px-5 font-heading font-semibold text-ris-dark text-xs uppercase tracking-wider">গ্রেড</th>
                            <th class="text-center py-3 px-5 font-heading font-semibold text-ris-dark text-xs uppercase tracking-wider">GP</th>
                            <th class="text-left py-3 px-5 font-heading font-semibold text-ris-dark text-xs uppercase tracking-wider">মন্তব্য</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $result)
                            <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                                <td class="py-3.5 px-5 font-medium text-gray-800">{{ $result->subject->name ?? $result->subject_name ?? '—' }}</td>
                                <td class="py-3.5 px-5 text-center text-gray-600">{{ $result->full_marks ?? '—' }}</td>
                                <td class="py-3.5 px-5 text-center font-medium text-gray-800">{{ $result->marks ?? '—' }}</td>
                                <td class="py-3.5 px-5 text-center">
                                    @if(isset($result->grade))
                                        @php
                                            $gradeColor = match(true) {
                                                in_array($result->grade, ['A+', 'A', 'A-', 'B']) => 'badge-success',
                                                in_array($result->grade, ['C', 'D']) => 'badge-warning',
                                                default => 'badge-danger',
                                            };
                                        @endphp
                                        <span class="badge {{ $gradeColor }} text-xs">{{ $result->grade }}</span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-5 text-center text-gray-600">{{ $result->gp ?? '—' }}</td>
                                <td class="py-3.5 px-5 text-gray-600 text-xs">{{ $result->remark ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50 border-t-2 border-gray-200">
                            <td class="py-3.5 px-5 font-heading font-bold text-ris-dark">মোট</td>
                            <td class="py-3.5 px-5 text-center font-heading font-bold text-ris-dark">{{ $results->sum('full_marks') }}</td>
                            <td class="py-3.5 px-5 text-center font-heading font-bold text-ris-dark">{{ $results->sum('marks') }}</td>
                            <td class="py-3.5 px-5 text-center">
                                @if(isset($gpa_summary['gpa']))
                                    <span class="badge badge text-xs">{{ $gpa_summary['grade'] ?? '—' }}</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5 text-center font-heading font-bold text-ris-primary">{{ $gpa_summary['gpa'] ?? '—' }}</td>
                            <td class="py-3.5 px-5"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="p-10 text-center">
                <div class="w-16 h-16 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h4 class="font-heading font-bold text-ris-dark">কোনো ফলাফল পাওয়া যায়নি</h4>
                <p class="text-sm text-ris-gray mt-1">নির্বাচিত পরীক্ষার জন্য কোনো ফলাফল প্রকাশিত হয়নি।</p>
            </div>
        @endif
    </div>

    {{-- Grade Legend --}}
    <div class="card p-5">
        <h4 class="font-heading font-semibold text-sm text-ris-dark mb-3">গ্রেড ব্যবস্থা</h4>
        <div class="flex flex-wrap items-center gap-4 text-xs">
            <div class="flex items-center gap-2">
                <span class="badge-success badge text-[10px] px-2 py-0.5">A+</span>
                <span class="text-gray-600">৮০-১০০</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="badge-success badge text-[10px] px-2 py-0.5">A</span>
                <span class="text-gray-600">৭০-৭৯</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="badge-success badge text-[10px] px-2 py-0.5">A-</span>
                <span class="text-gray-600">৬০-৬৯</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="badge-success badge text-[10px] px-2 py-0.5">B</span>
                <span class="text-gray-600">৫০-৫৯</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="badge-warning badge text-[10px] px-2 py-0.5">C</span>
                <span class="text-gray-600">৪০-৪৯</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="badge-warning badge text-[10px] px-2 py-0.5">D</span>
                <span class="text-gray-600">৩৩-৩৯</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="badge-danger badge text-[10px] px-2 py-0.5">F</span>
                <span class="text-gray-600">০-৩২</span>
            </div>
        </div>
    </div>

</div>

@endsection
