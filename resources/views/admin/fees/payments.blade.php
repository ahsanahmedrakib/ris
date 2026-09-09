@extends('layouts.admin')

@section('title', 'পেমেন্ট ইতিহাস')

@section('content')
<div class="space-y-6">

    <div class="flex items-center gap-4">
        <a href="{{ route('admin.fees.index') }}" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-heading font-bold text-gray-900">পেমেন্ট ইতিহাস</h1>
            <p class="text-sm text-gray-500 mt-1">সকল পেমেন্টের সম্পূর্ণ ইতিহাস</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">পেমেন্ট আইডি</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">ছাত্রের নাম</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">ইনভয়েস নং</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">পরিমাণ</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">পেমেন্ট মাধ্যম</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">তারিখ</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">রসিদ নং</th>
                        <th class="text-right px-5 py-3.5 font-medium text-gray-500">কার্যক্রম</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse(($payments ?? []) as $payment)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3.5 font-mono text-xs text-gray-500">#{{ $payment->id }}</td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-ris-primary/10 flex items-center justify-center text-ris-primary text-xs font-semibold shrink-0">
                                        {{ substr($payment->student->name_bn ?? 'ছ', 0, 1) }}
                                    </div>
                                    <span class="text-gray-900">{{ $payment->student->name_bn ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $payment->invoice->invoice_no ?? '-' }}</td>
                            <td class="px-5 py-3.5 font-medium text-emerald-600">৳{{ number_format($payment->amount) }}</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                    {{ $payment->method === 'cash' ? 'নগদ' : ($payment->method === 'bank' ? 'ব্যাংক' : ($payment->method === 'mobile' ? 'মোবাইল' : 'অনলাইন')) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-gray-600">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}</td>
                            <td class="px-5 py-3.5 text-gray-500 font-mono text-xs">{{ $payment->receipt_no ?? '-' }}</td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="#" class="p-1.5 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors" title="রসিদ দেখুন">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <p class="text-gray-500 font-medium">কোনো পেমেন্ট নেই</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($payments) && $payments instanceof \Illuminate\Pagination\LengthAwarePaginator && $payments->hasPages())
            <div class="px-5 py-3 border-t border-gray-100">
                {{ $payments->links() }}
            </div>
        @endif
    </div>

</div>
@endsection