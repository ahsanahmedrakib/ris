@extends('layouts.website')

@section('content')

    {{-- Hero --}}
    <section class="relative bg-cover bg-center overflow-hidden py-16 sm:py-20"
        style="background-image: url('{{ asset('assets/banner.png') }}')">
        <div class="absolute inset-0 bg-linear-to-r from-ris-dark via-ris-accent to-ris-light opacity-90"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal">
            <h1 class="font-heading font-bold text-3xl sm:text-4xl text-white">পরীক্ষার ফলাফল</h1>
            <p class="mt-3 text-white/70 text-lg">নাম, রোল ও শ্রেণি দিয়ে ফলাফল অনুসন্ধান করুন</p>
        </div>
    </section>

    <section class="py-16 sm:py-20 bg-white section-pattern-grid relative overflow-hidden">
        <div class="absolute top-10 left-10 w-40 h-40 bg-ris-primary/5 rounded-full pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Search Form --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-card p-6 sm:p-8 mb-10 reveal">
                <form method="GET" action="{{ route('academic.results') }}" class="space-y-4">
                    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">শ্রেণি <span class="text-red-500">*</span></label>
                            <select name="class_id" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                                <option value="">শ্রেণি নির্বাচন করুন</option>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}" {{ $selectedClass == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">রোল নম্বর</label>
                            <input type="text" name="roll_no" value="{{ request('roll_no') }}" placeholder="রোল নম্বর"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">নাম বা ভর্তি নম্বর</label>
                            <input type="text" name="search" value="{{ $search }}" placeholder="নাম বা ভর্তি নম্বর"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">পরীক্ষা</label>
                            <select name="exam_id" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                                <option value="">সকল পরীক্ষা</option>
                                @foreach ($exams as $exam)
                                    <option value="{{ $exam->id }}" {{ $examId == $exam->id ? 'selected' : '' }}>{{ $exam->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('academic.results') }}" class="px-5 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                            রিসেট
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm flex items-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            অনুসন্ধান করুন
                        </button>
                    </div>
                </form>
            </div>

            {{-- Results --}}
            @if ($students->isNotEmpty() && $examGroups->isNotEmpty())
                <div class="space-y-8 mb-8">
                    @foreach ($examGroups as $group)
                        @php $exam = $group['exam']; @endphp
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-card overflow-hidden reveal">
                            <div class="bg-ris-primary/5 px-6 py-4 border-b border-gray-100">
                                <h4 class="font-heading font-bold text-ris-dark text-lg">{{ $exam->name }}</h4>
                                <p class="text-sm text-gray-500">
                                    পূর্ণমান: {{ $exam->total_marks }} | পাস নম্বর: {{ $exam->passing_marks }}
                                </p>
                            </div>

                            {{-- Subject-wise tables --}}
                            <div class="grid lg:grid-cols-2 gap-6 p-6">
                                @foreach ($group['subjects'] as $subject)
                                    @php
                                        $rowsForSubject = $group['rowsBySubject']->get($subject->id, collect());
                                    @endphp
                                    <div class="rounded-xl border border-gray-100 overflow-hidden">
                                        <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                                            <h5 class="font-heading font-semibold text-ris-dark">{{ $subject->name }}</h5>
                                        </div>
                                        <div class="overflow-x-auto">
                                            <table class="w-full text-sm">
                                                <thead>
                                                    <tr class="bg-white border-b border-gray-50">
                                                        <th class="text-left px-4 py-2.5 text-gray-500 font-medium">রোল</th>
                                                        <th class="text-left px-4 py-2.5 text-gray-500 font-medium">শিক্ষার্থীর নাম</th>
                                                        <th class="text-center px-4 py-2.5 text-gray-500 font-medium">প্রাপ্ত নম্বর</th>
                                                        <th class="text-center px-4 py-2.5 text-gray-500 font-medium">গ্রেড</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($students as $student)
                                                        @php
                                                            $result = $rowsForSubject->get($student->id);
                                                            $gradeColor = match ($result?->grade) {
                                                                'A+', 'A' => 'bg-green-100 text-green-700',
                                                                'B+', 'B' => 'bg-blue-100 text-blue-700',
                                                                'C+', 'C' => 'bg-amber-100 text-amber-700',
                                                                default => 'bg-gray-100 text-gray-700',
                                                            };
                                                        @endphp
                                                        <tr class="border-t border-gray-50 hover:bg-gray-50/50 transition-colors">
                                                            <td class="px-4 py-2.5 text-gray-500">{{ $student->roll_no ?? '-' }}</td>
                                                            <td class="px-4 py-2.5 font-medium text-ris-dark">{{ $student->user->name }}</td>
                                                            <td class="px-4 py-2.5 text-center">
                                                                @if ($result)
                                                                    <span
                                                                        class="font-semibold text-ris-dark">{{ number_format($result->marks_obtained, 1) }}</span>
                                                                @else
                                                                    <span class="text-gray-300">-</span>
                                                                @endif
                                                            </td>
                                                            <td class="px-4 py-2.5 text-center">
                                                                @if ($result)
                                                                    <span
                                                                        class="inline-block px-2 py-0.5 text-xs font-semibold rounded-full align-middle {{ $gradeColor }}">{{ $result->grade }}</span>
                                                                @else
                                                                    <span class="text-gray-300">-</span>
                                                                @endif
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
                    @endforeach
                </div>
            @elseif ($selectedClass && $students->isEmpty())
                <div class="text-center py-16 bg-gray-50 rounded-2xl reveal">
                    <div class="w-16 h-16 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="font-heading font-bold text-lg text-ris-dark">কোনো শিক্ষার্থী পাওয়া যায়নি</h3>
                    <p class="mt-2 text-gray-500 text-sm">দয়া করে সঠিক তথ্য দিয়ে আবার অনুসন্ধান করুন।</p>
                </div>
            @elseif ($selectedClass && $students->isNotEmpty() && $examGroups->isEmpty())
                <div class="text-center py-16 bg-gray-50 rounded-2xl reveal">
                    <div class="w-16 h-16 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="font-heading font-bold text-lg text-ris-dark">এখনো কোনো ফলাফল প্রবেশ করা হয়নি</h3>
                    <p class="mt-2 text-gray-500 text-sm">এই শ্রেণির জন্য পরীক্ষার ফলাফল এখনো প্রকাশ করা হয়নি।</p>
                </div>
            @endif
        </div>
    </section>

@endsection