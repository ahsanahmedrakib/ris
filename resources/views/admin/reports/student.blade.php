@extends('layouts.admin')

@section('title', 'ছাত্র/ছাত্রী রিপোর্ট')

@section('content')
<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-heading font-bold text-gray-900">ছাত্র/ছাত্রী রিপোর্ট</h1>
        <p class="text-sm text-gray-500 mt-1">ছাত্রদের উপস্থিতি, পরীক্ষা ফলাফল ও সারসংক্ষেপ দেখুন</p>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">শ্রেণী</label>
                    <select name="class_id" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                        <option value="">সকল শ্রেণী</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ছাত্র</label>
                    <select name="student_id" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                        <option value="">ছাত্র নির্বাচন করুন</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>{{ $student->name_bn }} ({{ $student->admission_no }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors">
                        রিপোর্ট দেখুন
                    </button>
                    <a href="{{ route('admin.reports.students') }}" class="px-4 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                        রিসেট
                    </a>
                </div>
            </div>
        </form>
    </div>

    @if(isset($selectedStudent))
        {{-- Student Info Card --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-ris-primary/10 flex items-center justify-center text-ris-primary text-2xl font-heading font-bold shrink-0">
                    {{ substr($selectedStudent->name_bn, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-xl font-heading font-bold text-gray-900">{{ $selectedStudent->name_bn }}</h2>
                    <p class="text-sm text-gray-500">{{ $selectedStudent->name_en }}</p>
                </div>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-5 pt-5 border-t border-gray-100">
                <div>
                    <p class="text-xs text-gray-500">শ্রেণী</p>
                    <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $selectedStudent->class->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">ভর্তি নং</p>
                    <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $selectedStudent->admission_no }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">রোল নং</p>
                    <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $selectedStudent->roll_no }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">অবস্থা</p>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                        {{ ($selectedStudent->status ?? '') === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                        {{ ($selectedStudent->status ?? '') === 'active' ? 'সক্রিয়' : 'নিষ্ক্রিয়' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Attendance Summary --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center hover:shadow-md transition-shadow">
                <p class="text-2xl font-heading font-bold text-gray-900">{{ $selectedStudent->attendance_summary['total_days'] ?? 0 }}</p>
                <p class="text-xs text-gray-500 mt-1">মোট দিন</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center hover:shadow-md transition-shadow">
                <p class="text-2xl font-heading font-bold text-emerald-600">{{ $selectedStudent->attendance_summary['present'] ?? 0 }}</p>
                <p class="text-xs text-gray-500 mt-1">উপস্থিত</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center hover:shadow-md transition-shadow">
                <p class="text-2xl font-heading font-bold text-red-600">{{ $selectedStudent->attendance_summary['absent'] ?? 0 }}</p>
                <p class="text-xs text-gray-500 mt-1">অনুপস্থিত</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center hover:shadow-md transition-shadow">
                <p class="text-2xl font-heading font-bold text-ris-primary">{{ $selectedStudent->attendance_summary['percentage'] ?? 0 }}%</p>
                <p class="text-xs text-gray-500 mt-1">উপস্থিতির হার</p>
            </div>
        </div>

        {{-- Exam Results --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-heading font-semibold text-gray-900">পরীক্ষার ফলাফল</h2>
            </div>
            @php $groupedResults = ($selectedStudent->exam_results ?? collect())->groupBy('exam.name') @endphp
            @forelse($groupedResults as $examName => $results)
                <div class="{{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                    <div class="px-5 py-3 bg-gray-50">
                        <h3 class="text-sm font-semibold text-gray-700">{{ $examName }}</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="text-left px-5 py-3 font-medium text-gray-500">বিষয়</th>
                                    <th class="text-center px-5 py-3 font-medium text-gray-500">পূর্ণমান</th>
                                    <th class="text-center px-5 py-3 font-medium text-gray-500">প্রাপ্তমান</th>
                                    <th class="text-center px-5 py-3 font-medium text-gray-500">গ্রেড</th>
                                    <th class="text-center px-5 py-3 font-medium text-gray-500">বিভাগ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($results as $result)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-5 py-3 text-gray-900 font-medium">{{ $result->subject->name ?? '-' }}</td>
                                        <td class="px-5 py-3 text-center text-gray-600">{{ $result->full_marks ?? '-' }}</td>
                                        <td class="px-5 py-3 text-center text-gray-900 font-medium">{{ $result->marks ?? '-' }}</td>
                                        <td class="px-5 py-3 text-center">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                {{ ($result->grade ?? '') === 'A+' ? 'bg-emerald-50 text-emerald-700' : (($result->grade ?? '') === 'F' ? 'bg-red-50 text-red-700' : 'bg-blue-50 text-blue-700') }}">
                                                {{ $result->grade ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3 text-center text-gray-600">{{ $result->section ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="px-5 py-12 text-center">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <p class="text-gray-500 font-medium">কোনো পরীক্ষার ফলাফল পাওয়া যায়নি</p>
                </div>
            @endforelse
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 px-5 py-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <p class="text-gray-500 font-medium">রিপোর্ট দেখতে একজন ছাত্র নির্বাচন করুন</p>
            <p class="text-sm text-gray-400 mt-1">উপরের ফিল্টার ব্যবহার করে শ্রেণী ও ছাত্র নির্বাচন করুন</p>
        </div>
    @endif

</div>
@endsection
