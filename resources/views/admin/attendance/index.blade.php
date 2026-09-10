@extends('layouts.admin')

@section('title', 'উপস্থিতি ব্যবস্থাপনা')

@section('content')
    <div class="space-y-6" x-data="{ saving: false }">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-heading font-bold text-gray-900">উপস্থিতি ব্যবস্থাপনা</h1>
                <p class="text-sm text-gray-500 mt-1">দৈনিক উপস্থিতি রেকর্ড করুন ও পরিচালনা করুন</p>
            </div>
            <a href="{{ route('admin.attendance.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                নতুন উপস্থিতি নিন
            </a>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <form method="GET" action="{{ route('admin.attendance.index') }}">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">তারিখ</label>
                        <input type="date" name="date" value="{{ request('date', date('Y-m-d')) }}"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">শ্রেণি</label>
                        <select name="class_id"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                            <option value="">সকল শ্রেণি</option>
                            @foreach ($classes ?? [] as $class)
                                <option value="{{ $class->id }}"
                                    {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit"
                            class="px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors">
                            ফিল্টার করুন
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Summary Stats --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                <p class="text-2xl font-heading font-bold text-gray-900">{{ $totalStudents ?? 0 }}</p>
                <p class="text-xs text-gray-500 mt-1">মোট ছাত্র</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                <p class="text-2xl font-heading font-bold text-emerald-600">{{ $presentCount ?? 0 }}</p>
                <p class="text-xs text-gray-500 mt-1">উপস্থিত</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                <p class="text-2xl font-heading font-bold text-red-600">{{ $absentCount ?? 0 }}</p>
                <p class="text-xs text-gray-500 mt-1">অনুপস্থিত</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                <p class="text-2xl font-heading font-bold text-amber-600">{{ $lateCount ?? 0 }}</p>
                <p class="text-xs text-gray-500 mt-1">বিলম্বিত</p>
            </div>
        </div>

        {{-- Attendance Table --}}
        <form method="POST" action="{{ route('admin.attendance.store') }}" x-on:submit="saving = true">
            @csrf
            <input type="hidden" name="date" value="{{ request('date', date('Y-m-d')) }}">
            <input type="hidden" name="class_id" value="{{ request('class_id') }}">

            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-heading font-semibold text-gray-900">উপস্থিতি তালিকা</h2>
                    <button type="submit" :disabled="saving"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm disabled:opacity-50">
                        <svg x-show="!saving" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <svg x-show="saving" x-cloak class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                        </svg>
                        <span x-text="saving ? 'সংরক্ষণ হচ্ছে...' : 'সকল উপস্থিতি সংরক্ষণ করুন'"></span>
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="text-left px-5 py-3.5 font-medium text-gray-500">ক্রমিক</th>
                                <th class="text-left px-5 py-3.5 font-medium text-gray-500">নাম</th>
                                <th class="text-left px-5 py-3.5 font-medium text-gray-500">রোল নং</th>
                                <th class="text-center px-5 py-3.5 font-medium text-gray-500">উপস্থিত</th>
                                <th class="text-center px-5 py-3.5 font-medium text-gray-500">অনুপস্থিত</th>
                                <th class="text-center px-5 py-3.5 font-medium text-gray-500">বিলম্বিত</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse(($students ?? []) as $index => $student)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-5 py-3.5 text-gray-500">{{ $index + 1 }}</td>
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
                                    <td class="px-5 py-3.5 text-center">
                                        <label class="inline-flex items-center justify-center cursor-pointer">
                                            <input type="radio" name="attendance[{{ $student->id }}]" value="present"
                                                {{ ($student->today_status ?? '') === 'present' ? 'checked' : '' }}
                                                class="w-4 h-4 text-emerald-600 border-gray-300 focus:ring-emerald-500">
                                        </label>
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        <label class="inline-flex items-center justify-center cursor-pointer">
                                            <input type="radio" name="attendance[{{ $student->id }}]" value="absent"
                                                {{ ($student->today_status ?? '') === 'absent' ? 'checked' : '' }}
                                                class="w-4 h-4 text-red-600 border-gray-300 focus:ring-red-500">
                                        </label>
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        <label class="inline-flex items-center justify-center cursor-pointer">
                                            <input type="radio" name="attendance[{{ $student->id }}]" value="late"
                                                {{ ($student->today_status ?? '') === 'late' ? 'checked' : '' }}
                                                class="w-4 h-4 text-amber-600 border-gray-300 focus:ring-amber-500">
                                        </label>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-12 text-center text-gray-400 text-sm">এই শ্রেণিতে
                                        কোনো ছাত্র নেই</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </form>

    </div>
@endsection
