@extends('layouts.admin')

@section('title', 'পরীক্ষা বিস্তারিত')

@section('content')
    <div class="space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.exams.index') }}"
                    class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-heading font-bold text-gray-900">{{ $exam->name }}</h1>
                    <p class="text-sm text-gray-500 mt-1">পরীক্ষার বিস্তারিত তথ্য</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.exams.results', $exam) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    ফলাফল
                </a>
                <a href="{{ route('admin.exams.edit', $exam) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">
                    সম্পাদনা
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <p class="text-xs font-medium text-gray-400">ধরন</p>
                        <span
                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium mt-1
                        {{ $exam->type === 'midterm' ? 'bg-blue-50 text-blue-700' : ($exam->type === 'final' ? 'bg-purple-50 text-purple-700' : ($exam->type === 'quiz' ? 'bg-cyan-50 text-cyan-700' : 'bg-violet-50 text-violet-700')) }}">
                            {{ $exam->type === 'quiz' ? 'কুইজ' : ($exam->type === 'midterm' ? 'অর্ধবার্ষিক' : ($exam->type === 'final' ? 'বার্ষিক' : 'এসাইনমেন্ট')) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-400">শ্রেণি</p>
                        <p class="text-sm text-gray-700 mt-1">{{ $exam->classRoom->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-400">শিক্ষাবর্ষ</p>
                        <p class="text-sm text-gray-700 mt-1">{{ $exam->academicYear->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-400">প্রবেশকৃত ফলাফল</p>
                        <p class="text-sm text-gray-700 mt-1">{{ $exam->exam_results_count ?? 0 }}টি</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-400">শুরুর তারিখ</p>
                        <p class="text-sm text-gray-700 mt-1">{{ $exam->start_date?->format('d/m/Y') ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-400">শেষ তারিখ</p>
                        <p class="text-sm text-gray-700 mt-1">{{ $exam->end_date?->format('d/m/Y') ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-400">মোট নম্বর</p>
                        <p class="text-sm text-gray-700 mt-1">{{ $exam->total_marks }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-400">পাস নম্বর</p>
                        <p class="text-sm text-gray-700 mt-1">{{ $exam->passing_marks }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-heading font-semibold text-gray-900">ফলাফল তালিকা</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">ক্রমিক</th>
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">ছাত্র/ছাত্রী</th>
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">বিষয়</th>
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">প্রাপ্ত নম্বর</th>
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">গ্রেড</th>
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">মন্তব্য</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($exam->examResults as $index => $result)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3.5 text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-5 py-3.5">
                                    <p class="font-medium text-gray-900">{{ $result->student->user->name ?? '-' }}</p>
                                    <p class="text-xs text-gray-400">রোল: {{ $result->student->roll_no ?? '-' }}</p>
                                </td>
                                <td class="px-5 py-3.5 text-gray-600">{{ $result->subject->name ?? '-' }}</td>
                                <td class="px-5 py-3.5 font-medium text-gray-900">{{ $result->marks_obtained }}</td>
                                <td class="px-5 py-3.5">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">{{ $result->grade ?? '-' }}</span>
                                </td>
                                <td class="px-5 py-3.5 text-gray-600">{{ $result->remarks ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-gray-400 text-sm">এখনো কোনো ফলাফল
                                    প্রবেশ করা হয়নি</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
