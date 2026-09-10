@extends('layouts.admin')

@section('title', 'ছাত্রের বিবরণ')

@section('content')
    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.students.index') }}"
                class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div class="flex-1">
                <h1 class="text-2xl font-heading font-bold text-gray-900">ছাত্রের বিবরণ</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $student->name_bn ?? '' }} -এর সম্পূর্ণ তথ্য</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.students.edit', $student) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    সম্পাদনা
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Profile Card --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="bg-linear-to-r from-ris-primary to-ris-dark p-6 text-center">
                    <div
                        class="w-20 h-20 rounded-full bg-white/20 flex items-center justify-center text-white text-2xl font-heading font-bold mx-auto border-4 border-white/30">
                        {{ substr($student->name_bn ?? 'ছ', 0, 1) }}
                    </div>
                    <h2 class="text-white font-heading font-bold text-lg mt-3">{{ $student->name_bn ?? '' }}</h2>
                    <p class="text-white/70 text-sm">{{ $student->name_en ?? '' }}</p>
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/20 text-white mt-2">
                        ভর্তি নং: {{ $student->admission_no ?? '' }}
                    </span>
                </div>
                <div class="p-5 space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">শ্রেণি</span>
                        <span class="font-medium text-gray-900">{{ $student->class->name ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">রোল নং</span>
                        <span class="font-medium text-gray-900">{{ $student->roll_no ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">লিঙ্গ</span>
                        <span class="font-medium text-gray-900">{{ $student->gender === 'male' ? 'পুরুষ' : 'মহিলা' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">জন্ম তারিখ</span>
                        <span
                            class="font-medium text-gray-900">{{ $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('d/m/Y') : '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">রক্তের গ্রুপ</span>
                        <span class="font-medium text-gray-900">{{ $student->blood_group ?? '-' }}</span>
                    </div>
                    <hr class="border-gray-100">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">অবস্থা</span>
                        <span
                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                        {{ ($student->status ?? '') === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                            {{ ($student->status ?? '') === 'active' ? 'সক্রিয়' : 'নিষ্ক্রিয়' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-6">

                {{-- Guardian Info --}}
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h3 class="font-heading font-semibold text-gray-900">অভিভাবক তথ্য</h3>
                    </div>
                    <div class="p-5 grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <div>
                            <p class="text-xs text-gray-400 mb-1">অভিভাবকের নাম</p>
                            <p class="text-sm font-medium text-gray-900">{{ $student->guardian_name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-1">মোবাইল</p>
                            <p class="text-sm font-medium text-gray-900">{{ $student->guardian_phone ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-1">ইমেইল</p>
                            <p class="text-sm font-medium text-gray-900">{{ $student->guardian_email ?? '-' }}</p>
                        </div>
                        <div class="sm:col-span-3">
                            <p class="text-xs text-gray-400 mb-1">ঠিকানা</p>
                            <p class="text-sm font-medium text-gray-900">{{ $student->address ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Attendance Summary --}}
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h3 class="font-heading font-semibold text-gray-900">উপস্থিতি সারসংক্ষেপ</h3>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div class="text-center p-3 bg-emerald-50 rounded-lg">
                                <p class="text-2xl font-heading font-bold text-emerald-600">
                                    {{ $attendanceSummary['present'] ?? 180 }}</p>
                                <p class="text-xs text-emerald-600 mt-1">উপস্থিত</p>
                            </div>
                            <div class="text-center p-3 bg-red-50 rounded-lg">
                                <p class="text-2xl font-heading font-bold text-red-600">
                                    {{ $attendanceSummary['absent'] ?? 12 }}</p>
                                <p class="text-xs text-red-600 mt-1">অনুপস্থিত</p>
                            </div>
                            <div class="text-center p-3 bg-amber-50 rounded-lg">
                                <p class="text-2xl font-heading font-bold text-amber-600">
                                    {{ $attendanceSummary['late'] ?? 8 }}</p>
                                <p class="text-xs text-amber-600 mt-1">বিলম্বিত</p>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between text-sm mb-1.5">
                                <span class="text-gray-600">উপস্থিতির হার</span>
                                <span
                                    class="font-medium text-gray-900">{{ $attendanceSummary['percentage'] ?? '90%' }}</span>
                            </div>
                            <div class="w-full h-2.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-emerald-500 rounded-full"
                                    style="width: {{ $attendanceSummary['percentage_value'] ?? 90 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Fee Status --}}
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h3 class="font-heading font-semibold text-gray-900">ফি অবস্থা</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-100">
                                    <th class="text-left px-5 py-3 font-medium text-gray-500">ফি ধরন</th>
                                    <th class="text-left px-5 py-3 font-medium text-gray-500">পরিমাণ</th>
                                    <th class="text-left px-5 py-3 font-medium text-gray-500">পরিশোধিত</th>
                                    <th class="text-left px-5 py-3 font-medium text-gray-500">বকেয়</th>
                                    <th class="text-left px-5 py-3 font-medium text-gray-500">অবস্থা</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse(($feeStatus ?? []) as $fee)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-5 py-3 text-gray-900">{{ $fee['type'] ?? '-' }}</td>
                                        <td class="px-5 py-3 text-gray-600">৳{{ number_format($fee['amount'] ?? 0) }}</td>
                                        <td class="px-5 py-3 text-gray-600">৳{{ number_format($fee['paid'] ?? 0) }}</td>
                                        <td class="px-5 py-3 text-gray-600">৳{{ number_format($fee['due'] ?? 0) }}</td>
                                        <td class="px-5 py-3">
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                            {{ ($fee['status'] ?? '') === 'paid' ? 'bg-emerald-50 text-emerald-700' : (($fee['status'] ?? '') === 'partial' ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700') }}">
                                                {{ ($fee['status'] ?? '') === 'paid' ? 'পরিশোধিত' : (($fee['status'] ?? '') === 'partial' ? 'আংশিক' : 'বকেয়') }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-5 py-6 text-center text-gray-400 text-sm">কোনো ফি
                                            তথ্য নেই</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Exam Results --}}
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h3 class="font-heading font-semibold text-gray-900">পরীক্ষার ফলাফল</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-100">
                                    <th class="text-left px-5 py-3 font-medium text-gray-500">পরীক্ষা</th>
                                    <th class="text-left px-5 py-3 font-medium text-gray-500">বিষয়</th>
                                    <th class="text-left px-5 py-3 font-medium text-gray-500">পূর্ণমান</th>
                                    <th class="text-left px-5 py-3 font-medium text-gray-500">প্রাপ্ত নম্বর</th>
                                    <th class="text-left px-5 py-3 font-medium text-gray-500">গ্রেড</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse(($examResults ?? []) as $result)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-5 py-3 text-gray-900">{{ $result['exam'] ?? '-' }}</td>
                                        <td class="px-5 py-3 text-gray-600">{{ $result['subject'] ?? '-' }}</td>
                                        <td class="px-5 py-3 text-gray-600">{{ $result['total'] ?? '-' }}</td>
                                        <td class="px-5 py-3 font-medium text-gray-900">{{ $result['marks'] ?? '-' }}</td>
                                        <td class="px-5 py-3">
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                            {{ ($result['grade'] ?? '') === 'A+' ? 'bg-emerald-50 text-emerald-700' : (($result['grade'] ?? '') === 'F' ? 'bg-red-50 text-red-700' : 'bg-blue-50 text-blue-700') }}">
                                                {{ $result['grade'] ?? '-' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-5 py-6 text-center text-gray-400 text-sm">কোনো
                                            পরীক্ষার ফলাফল নেই</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection
