@extends('layouts.admin')

@section('title', 'বেতন ব্যবস্থাপনা')

@section('content')
<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-heading font-bold text-gray-900">বেতন ব্যবস্থাপনা</h1>
        <p class="text-sm text-gray-500 mt-1">কর্মচারীদের বেতন প্রক্রিয়াকরণ ও ব্যবস্থাপনা</p>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 lg:gap-6">
        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">মোট কর্মচারী</p>
                    <p class="text-2xl font-heading font-bold text-gray-900 mt-1">{{ $totalStaff ?? 45 }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">এই মাসের বেতন</p>
                    <p class="text-2xl font-heading font-bold text-ris-primary mt-1">৳{{ number_format($totalPayroll ?? 450000) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-ris-primary/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-ris-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">ইতিমধ্যে পরিশোধিত</p>
                    <p class="text-2xl font-heading font-bold text-emerald-600 mt-1">৳{{ number_format($paidPayroll ?? 380000) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Process Payroll --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-heading font-semibold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-ris-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                বেতন প্রক্রিয়াকরণ
            </h2>
        </div>
        <div class="p-5">
            <form method="POST" action="{{ route('admin.payroll.process') }}" class="space-y-5">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">মাস <span class="text-red-500">*</span></label>
                        <select name="month" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                            <option value="">মাস নির্বাচন করুন</option>
                            @foreach(['জানুয়ারি','ফেব্রুয়ারি','মার্চ','এপ্রিল','মে','জুন','জুলাই','আগস্ট','সেপ্টেম্বর','অক্টোবর','নভেম্বর','ডিসেম্বর'] as $i => $month)
                                <option value="{{ $i + 1 }}" {{ old('month') == ($i + 1) ? 'selected' : '' }}>{{ $month }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">বছর <span class="text-red-500">*</span></label>
                        <select name="year" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                            <option value="">বছর নির্বাচন করুন</option>
                            @foreach([date('Y'), date('Y') - 1] as $y)
                                <option value="{{ $y }}" {{ old('year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" onclick="return confirm('আপনি কি এই মাসের বেতন প্রক্রিয়াকরণ করতে চান?')" class="px-6 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm w-full sm:w-auto">
                            বেতন প্রক্রিয়াকরণ করুন
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Payroll List --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-heading font-semibold text-gray-900">সাম্প্রতিক বেতন তালিকা</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">ক্রমিক</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">কর্মচারী</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">পদবি</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">মূল বেতন</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">কাটা</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">নেট বেতন</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">মাস</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">অবস্থা</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse(($payrolls ?? []) as $index => $pay)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3.5 text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-ris-primary/10 flex items-center justify-center text-ris-primary text-xs font-semibold shrink-0">
                                        {{ substr($pay->staff->name ?? 'ক', 0, 1) }}
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $pay->staff->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $pay->staff->designation ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-gray-600">৳{{ number_format($pay->basic_salary ?? 0) }}</td>
                            <td class="px-5 py-3.5 text-red-600">৳{{ number_format($pay->deduction ?? 0) }}</td>
                            <td class="px-5 py-3.5 font-medium text-gray-900">৳{{ number_format($pay->net_salary ?? 0) }}</td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $pay->month ?? '-' }}/{{ $pay->year ?? '' }}</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ ($pay->status ?? '') === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                    {{ ($pay->status ?? '') === 'paid' ? 'পরিশোধিত' : 'বকেয়' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center text-gray-400 text-sm">কোনো বেতন তথ্য নেই</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection