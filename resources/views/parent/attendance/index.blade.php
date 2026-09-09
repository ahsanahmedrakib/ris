@extends('layouts.parent')

@section('title', 'উপস্থিতি — অভিভাবক পোর্টাল')
@section('page-title', 'উপস্থিতি')

@section('content')

<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="font-heading font-bold text-xl text-ris-dark">উপস্থিতি</h2>
            <p class="text-sm text-ris-gray mt-1">সন্তানের উপস্থিতি রেকর্ড দেখুন</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card p-4 sm:p-5">
        <form method="GET" action="{{ route('parent.attendance') }}" class="flex flex-col sm:flex-row items-stretch sm:items-end gap-4">
            {{-- Child Selector --}}
            @if(!empty($children) && count($children) > 1)
                <div class="flex-1">
                    <label class="block text-xs font-medium text-ris-gray mb-1.5">সন্তান নির্বাচন করুন</label>
                    <select name="child_id" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-ris-primary/30 focus:border-ris-primary transition-all">
                        @foreach($children as $child)
                            <option value="{{ $child->id }}" {{ (request('child_id', $selected_child_id ?? null) == $child->id) ? 'selected' : '' }}>
                                {{ $child->name }} — {{ $child->class->name ?? '' }} {{ $child->section->name ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- Month Selector --}}
            <div class="w-full sm:w-44">
                <label class="block text-xs font-medium text-ris-gray mb-1.5">মাস</label>
                <select name="month" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-ris-primary/30 focus:border-ris-primary transition-all">
                    @php
                        $banglaMonths = ['জানুয়ারি', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর'];
                    @endphp
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ (request('month', date('m')) == $m) ? 'selected' : '' }}>
                            {{ $banglaMonths[$m - 1] }}
                        </option>
                    @endfor
                </select>
            </div>

            {{-- Year Selector --}}
            <div class="w-full sm:w-32">
                <label class="block text-xs font-medium text-ris-gray mb-1.5">বছর</label>
                <select name="year" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-ris-primary/30 focus:border-ris-primary transition-all">
                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}" {{ (request('year', date('Y')) == $y) ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-primary py-2.5 px-5 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                খুঁজুন
            </button>
        </form>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
        <div class="card p-4 text-center">
            <p class="text-2xl font-heading font-bold text-ris-dark">{{ $summary['total_days'] ?? 0 }}</p>
            <p class="text-xs text-ris-gray mt-1">মোট কার্যদিবস</p>
        </div>
        <div class="card p-4 text-center">
            <p class="text-2xl font-heading font-bold text-emerald-600">{{ $summary['present'] ?? 0 }}</p>
            <p class="text-xs text-ris-gray mt-1">উপস্থিত</p>
        </div>
        <div class="card p-4 text-center">
            <p class="text-2xl font-heading font-bold text-red-600">{{ $summary['absent'] ?? 0 }}</p>
            <p class="text-xs text-ris-gray mt-1">অনুপস্থিত</p>
        </div>
        <div class="card p-4 text-center">
            <p class="text-2xl font-heading font-bold text-amber-600">{{ $summary['late'] ?? 0 }}</p>
            <p class="text-xs text-ris-gray mt-1">বিলম্বিত</p>
        </div>
        <div class="card p-4 text-center">
            <p class="text-2xl font-heading font-bold text-ris-primary">{{ $summary['percentage'] ?? 0 }}%</p>
            <p class="text-xs text-ris-gray mt-1">উপস্থিতির হার</p>
        </div>
    </div>

    {{-- Attendance Table --}}
    <div class="card overflow-hidden">
        <div class="p-5 border-b border-gray-100">
            <h3 class="font-heading font-bold text-ris-dark">উপস্থিতির বিবরণ</h3>
        </div>

        @if(!empty($attendance_records) && count($attendance_records) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="text-left py-3 px-5 font-heading font-semibold text-ris-dark text-xs uppercase tracking-wider">তারিখ</th>
                            <th class="text-left py-3 px-5 font-heading font-semibold text-ris-dark text-xs uppercase tracking-wider">দিন</th>
                            <th class="text-center py-3 px-5 font-heading font-semibold text-ris-dark text-xs uppercase tracking-wider">অবস্থা</th>
                            <th class="text-left py-3 px-5 font-heading font-semibold text-ris-dark text-xs uppercase tracking-wider">প্রবেশ সময়</th>
                            <th class="text-left py-3 px-5 font-heading font-semibold text-ris-dark text-xs uppercase tracking-wider">মন্তব্য</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendance_records as $record)
                            <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                                <td class="py-3.5 px-5 font-medium text-gray-800">
                                    {{ isset($record->date) ? \Carbon\Carbon::parse($record->date)->format('d M, Y') : '—' }}
                                </td>
                                <td class="py-3.5 px-5 text-gray-600">
                                    {{ isset($record->date) ? \Carbon\Carbon::parse($record->date)->translatedFormat('l') : '—' }}
                                </td>
                                <td class="py-3.5 px-5 text-center">
                                    @if($record->status === 'present')
                                        <span class="badge-success badge text-xs">উপস্থিত</span>
                                    @elseif($record->status === 'absent')
                                        <span class="badge-danger badge text-xs">অনুপস্থিত</span>
                                    @elseif($record->status === 'late')
                                        <span class="badge-warning badge text-xs">বিলম্বিত</span>
                                    @elseif($record->status === 'holiday')
                                        <span class="badge text-xs bg-gray-500">ছুটি</span>
                                    @else
                                        <span class="badge text-xs">{{ $record->status ?? '—' }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-5 text-gray-600">{{ $record->check_in ?? '—' }}</td>
                                <td class="py-3.5 px-5 text-gray-600 text-xs">{{ $record->remark ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-gray-100">
                {{ $attendance_records->withQueryString()->links() }}
            </div>
        @else
            <div class="p-10 text-center">
                <div class="w-16 h-16 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <h4 class="font-heading font-bold text-ris-dark">কোনো তথ্য পাওয়া যায়নি</h4>
                <p class="text-sm text-ris-gray mt-1">নির্বাচিত মাসে কোনো উপস্থিতির রেকর্ড পাওয়া যায়নি।</p>
            </div>
        @endif
    </div>

    {{-- Legend --}}
    <div class="card p-5">
        <h4 class="font-heading font-semibold text-sm text-ris-dark mb-3">ব্যাখ্যা</h4>
        <div class="flex flex-wrap items-center gap-4 text-xs">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                <span class="text-gray-600">উপস্থিত</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-red-500"></span>
                <span class="text-gray-600">অনুপস্থিত</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                <span class="text-gray-600">বিলম্বিত</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-gray-400"></span>
                <span class="text-gray-600">ছুটি</span>
            </div>
        </div>
    </div>

</div>

@endsection
