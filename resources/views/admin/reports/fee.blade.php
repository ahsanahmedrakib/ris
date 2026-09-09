@extends('layouts.admin')

@section('title', 'ফি রিপোর্ট')

@section('content')
<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-heading font-bold text-gray-900">ফি রিপোর্ট</h1>
        <p class="text-sm text-gray-500 mt-1">ফি সংগ্রহ, বকেয় ও পেমেন্ট ইতিহাসের রিপোর্ট দেখুন</p>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 lg:gap-6">
        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">মোট সংগৃহীত</p>
                    <p class="text-2xl font-heading font-bold text-emerald-600 mt-1">৳{{ number_format($summary['collected'] ?? 0) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">বকেয়</p>
                    <p class="text-2xl font-heading font-bold text-amber-600 mt-1">৳{{ number_format($summary['pending'] ?? 0) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">মেয়াদোত্তীর্ণ</p>
                    <p class="text-2xl font-heading font-bold text-red-600 mt-1">৳{{ number_format($summary['overdue'] ?? 0) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment History --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-heading font-semibold text-gray-900">পেমেন্ট ইতিহাস</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">ক্রমিক</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">তারিখ</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">ছাত্রের নাম</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">ইনভয়েস নং</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">পরিমাণ</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">পেমেন্ট পদ্ধতি</th>
                        <th class="text-center px-5 py-3.5 font-medium text-gray-500">অবস্থা</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($payments as $index => $payment)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3.5 text-gray-500">{{ ($payments->currentPage() - 1) * $payments->perPage() + $index + 1 }}</td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $payment->date ? \Carbon\Carbon::parse($payment->date)->format('d/m/Y') : ($payment->created_at ? $payment->created_at->format('d/m/Y') : '-') }}</td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-ris-primary/10 flex items-center justify-center text-ris-primary text-xs font-semibold shrink-0">
                                        {{ substr($payment->student->name_bn ?? '?', 0, 1) }}
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $payment->student->name_bn ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 font-mono text-xs text-gray-500">{{ $payment->invoice_no ?? $payment->invoice->invoice_no ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-gray-900 font-medium">৳{{ number_format($payment->amount ?? 0) }}</td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $payment->method ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ ($payment->status ?? '') === 'paid' ? 'bg-emerald-50 text-emerald-700' : (($payment->status ?? '') === 'pending' ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700') }}">
                                    {{ ($payment->status ?? '') === 'paid' ? 'পরিশোধিত' : (($payment->status ?? '') === 'pending' ? 'পেন্ডিং' : 'বকেয়') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <p class="text-gray-500 font-medium">কোনো পেমেন্ট রেকর্ড পাওয়া যায়নি</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
            <div class="px-5 py-3 border-t border-gray-100">
                {{ $payments->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
