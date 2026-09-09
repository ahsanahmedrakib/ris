@extends('layouts.admin')

@section('title', 'উপস্থিতি বিস্তারিত')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.attendance.index') }}" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-heading font-bold text-gray-900">উপস্থিতি বিস্তারিত</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $attendance->date?->format('d/m/Y') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.attendance.edit', $attendance) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                সম্পাদনা
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <p class="text-xs font-medium text-gray-400">ছাত্র/ছাত্রী</p>
                    <p class="text-sm text-gray-700 mt-1 font-medium">{{ $attendance->student->user->name ?? '-' }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">রোল: {{ $attendance->student->roll_no ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400">শ্রেণী</p>
                    <p class="text-sm text-gray-700 mt-1">{{ $attendance->classRoom->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400">তারিখ</p>
                    <p class="text-sm text-gray-700 mt-1">{{ $attendance->date?->format('d/m/Y') ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400">অবস্থা</p>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium mt-1
                        {{ $attendance->status === 'present' ? 'bg-emerald-50 text-emerald-700' : ($attendance->status === 'late' ? 'bg-amber-50 text-amber-700' : ($attendance->status === 'excused' ? 'bg-blue-50 text-blue-700' : 'bg-red-50 text-red-700')) }}">
                        {{ $attendance->status === 'present' ? 'উপস্থিত' : ($attendance->status === 'late' ? 'দেরিতে' : ($attendance->status === 'excused' ? 'ছাড়পত্র' : 'অনুপস্থিত')) }}
                    </span>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400">মন্তব্য</p>
                    <p class="text-sm text-gray-700 mt-1">{{ $attendance->remarks ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400">নিয়ে ছিলেন</p>
                    <p class="text-sm text-gray-700 mt-1">{{ $attendance->marker->name ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection