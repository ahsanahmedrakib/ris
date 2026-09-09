@extends('layouts.admin')

@section('title', 'বেতন স্লিপ')

@section('content')
@php
    $months = ['', 'জানুয়ারি', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর'];
@endphp

<div class="space-y-6">

    <div class="flex items-center gap-4">
        <a href="{{ route('admin.payroll.index') }}" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-heading font-bold text-gray-900">বেতন স্লিপ</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $months[$payroll->month] ?? $payroll->month }}, {{ $payroll->year }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3 text-sm text-emerald-800 flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm text-red-800 flex items-center gap-3">
            <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-heading font-semibold text-gray-900">{{ $payroll->staff->user->name ?? '-' }}</h2>
                <p class="text-xs text-gray-400 mt-0.5">{{ $payroll->staff->employee_id ?? '' }}</p>
            </div>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                {{ $payroll->status === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                {{ $payroll->status === 'paid' ? 'পরিশোধিত' : 'মুলতুবি' }}
            </span>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <p class="text-xs font-medium text-gray-400">পদবি</p>
                    <p class="text-sm text-gray-700 mt-1">{{ $payroll->staff->designation ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400">মূল বেতন</p>
                    <p class="text-sm text-gray-700 mt-1">৳{{ number_format($payroll->basic_salary, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400">ভাতা</p>
                    <p class="text-sm text-gray-700 mt-1">৳{{ number_format($payroll->allowances, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400">ছাড়</p>
                    <p class="text-sm text-gray-700 mt-1">৳{{ number_format($payroll->deductions, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400">নিট বেতন</p>
                    <p class="text-sm text-ris-primary mt-1 font-bold text-lg">৳{{ number_format($payroll->net_salary, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400">পরিশোধের তারিখ</p>
                    <p class="text-sm text-gray-700 mt-1">{{ $payroll->paid_at?->format('d/m/Y') ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden h-fit">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-heading font-semibold text-gray-900">অবস্থা হালনাগাদ</h2>
        </div>
        <div class="p-5">
            <form method="POST" action="{{ route('admin.payroll.update', $payroll) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">অবস্থা <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                        <option value="pending" {{ old('status', $payroll->status) === 'pending' ? 'selected' : '' }}>মুলতুবি</option>
                        <option value="paid" {{ old('status', $payroll->status) === 'paid' ? 'selected' : '' }}>পরিশোধিত</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ভাতা (আপডেট)</label>
                    <input type="number" name="allowances" value="{{ old('allowances', $payroll->allowances) }}" min="0" step="0.01"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ছাড় (আপডেট)</label>
                    <input type="number" name="deductions" value="{{ old('deductions', $payroll->deductions) }}" min="0" step="0.01"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                </div>
                <button type="submit" class="w-full px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">
                    হালনাগাদ করুন
                </button>
            </form>
        </div>
    </div>

</div>
@endsection