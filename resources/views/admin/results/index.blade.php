@extends('layouts.admin')

@section('title', 'ফলাফল তালিকা')

@section('content')
    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-heading font-bold text-gray-900">সকল শিক্ষার্থীর ফলাফল</h1>
                <p class="text-sm text-gray-500 mt-1">শ্রেণি বা পরীক্ষা অনুযায়ী ফলাফল দেখুন ও এক্সেল ডাউনলোড করুন</p>
            </div>
            <a href="{{ route('admin.results.export', array_filter(request(['class_id', 'exam_id']))) }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                এক্সেল ডাউনলোড
            </a>
        </div>

        {{-- Filter --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4 sm:p-5">
            <form method="GET" action="{{ route('admin.results.index') }}" class="grid sm:grid-cols-3 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">শ্রেণি</label>
                    <select name="class_id"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                        <option value="">সকল শ্রেণি</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}" {{ $classId == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">পরীক্ষা</label>
                    <select name="exam_id"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                        <option value="">সকল পরীক্ষা</option>
                        @foreach ($exams as $exam)
                            <option value="{{ $exam->id }}" {{ $examId == $exam->id ? 'selected' : '' }}>
                                {{ $exam->name }} — {{ $exam->classRoom?->name ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-3">
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors cursor-pointer">ফিল্টার
                        করুন</button>
                    <a href="{{ route('admin.results.index') }}"
                        class="px-4 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">রিসেট</a>
                </div>
            </form>
        </div>

        {{-- Results --}}
        @php
            $gradeColors = [
                'A+' => 'bg-green-100 text-green-700',
                'A' => 'bg-green-100 text-green-700',
                'A-' => 'bg-emerald-100 text-emerald-700',
                'B+' => 'bg-blue-100 text-blue-700',
                'B' => 'bg-blue-100 text-blue-700',
                'C+' => 'bg-amber-100 text-amber-700',
                'C' => 'bg-amber-100 text-amber-700',
                'D' => 'bg-orange-100 text-orange-700',
                'F' => 'bg-red-100 text-red-700',
            ];
        @endphp

        @if ($groups->isNotEmpty())
            <div class="space-y-6">
                @foreach ($groups as $group)
                    @php
                        $exam = $group['exam'];
                    @endphp
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                        <div class="gradient-logo px-6 py-4 flex flex-wrap items-center justify-between gap-2">
                            <div>
                                <h3 class="font-heading font-bold text-white text-lg">{{ $exam->name }}</h3>
                                <p class="text-white/70 text-xs mt-0.5">
                                    শ্রেণি: {{ $exam->classRoom?->name ?? '-' }} |
                                    পূর্ণমান: {{ $exam->total_marks }} |
                                    পাস: {{ $exam->passing_marks }} |
                                    শিক্ষার্থী: {{ $group['students']->count() }} জন
                                </p>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="text-left px-4 py-3 text-gray-500 font-medium whitespace-nowrap">ক্রম</th>
                                        <th class="text-left px-4 py-3 text-gray-500 font-medium whitespace-nowrap">রোল</th>
                                        <th class="text-left px-4 py-3 text-gray-500 font-medium whitespace-nowrap">শিক্ষার্থীর নাম</th>
                                        @foreach ($group['subjects'] as $subject)
                                            <th class="text-center px-4 py-3 text-gray-500 font-medium whitespace-nowrap">
                                                {{ $subject->name }}</th>
                                        @endforeach
                                        <th class="text-center px-4 py-3 text-gray-500 font-medium whitespace-nowrap">মোট</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach ($group['students'] as $bean)
                                        @php
                                            $student = $bean['student'];
                                            $marks = $bean['marks'];
                                            $total = $marks->sum('marks_obtained');
                                        @endphp
                                        <tr class="hover:bg-gray-50/70 transition-colors">
                                            <td class="px-4 py-3 text-gray-500">{{ $loop->iteration }}</td>
                                            <td class="px-4 py-3 text-gray-600">{{ $student->roll_no ?? '-' }}</td>
                                            <td class="px-4 py-3 font-heading font-semibold text-ris-dark whitespace-nowrap">
                                                {{ $student->user?->name ?? '-' }}</td>
                                            @foreach ($group['subjects'] as $subject)
                                                @php
                                                    $result = $marks->get($subject->id);
                                                @endphp
                                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                                    @if ($result)
                                                        <span class="font-semibold text-gray-800">{{ $result->marks_obtained }}</span>
                                                        <span
                                                            class="ml-1 inline-block px-2 py-0.5 text-xs font-semibold rounded-full {{ $gradeColors[$result->grade] ?? 'bg-gray-100 text-gray-700' }}">
                                                            {{ $result->grade }}
                                                        </span>
                                                    @else
                                                        <span class="text-gray-300">-</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                            <td class="px-4 py-3 text-center">
                                                <span class="font-bold text-ris-primary">{{ number_format($total, 2) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-16 bg-white rounded-xl border border-gray-200">
                <div class="w-16 h-16 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="font-heading font-bold text-lg text-gray-800">কোনো ফলাফল পাওয়া যায়নি</h3>
                <p class="mt-2 text-gray-500 text-sm">প্রথমে পরীক্ষার ফলাফল প্রবেশ করুন বা ফিল্টার পরিবর্তন করুন।</p>
            </div>
        @endif
    </div>
@endsection