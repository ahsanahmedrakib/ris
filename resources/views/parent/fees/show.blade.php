@extends('layouts.parent')

@section('title', 'ইনভয়েস বিস্তারিত — অভিভাবক পোর্টাল')
@section('page-title', 'ইনভয়েস বিস্তারিত')

@section('content')

    <div class="space-y-6">

        {{-- Back Link --}}
        <a href="{{ route('parent.fees') }}"
            class="inline-flex items-center gap-1.5 text-sm font-medium text-ris-primary hover:text-ris-dark transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            ফি তালিকায় ফিরুন
        </a>

        {{-- Invoice Header --}}
        <div class="card overflow-hidden">
            <div class="bg-linear-to-r from-ris-dark to-ris-primary p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="font-heading font-bold text-2xl text-white">ইনভয়েস
                            #{{ $invoice->invoice_no ?? $invoice->id }}</h2>
                        <p class="text-white/70 text-sm mt-1">
                            {{ $invoice->description ?? ($invoice->fee_type ?? 'ফি ইনভয়েস') }}</p>
                    </div>
                    <div class="text-left sm:text-right">
                        @if (isset($invoice->status) && $invoice->status === 'paid')
                            <span
                                class="inline-flex items-center gap-1.5 bg-emerald-500 text-white text-sm font-heading font-semibold px-4 py-2 rounded-full">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                পরিশোধিত
                            </span>
                        @elseif(isset($invoice->status) && $invoice->status === 'partial')
                            <span
                                class="inline-flex items-center gap-1.5 bg-amber-500 text-white text-sm font-heading font-semibold px-4 py-2 rounded-full">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                আংশিক পরিশোধিত
                            </span>
                        @else
                            <span
                                class="inline-flex items-center gap-1.5 bg-red-500 text-white text-sm font-heading font-semibold px-4 py-2 rounded-full">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                বকেয়া
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Invoice Details --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Amount Info --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="card p-6">
                    <h3 class="font-heading font-bold text-ris-dark mb-5">ইনভয়েস বিবরণ</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                            <span class="text-ris-gray text-sm">ছাত্র/ছাত্রী</span>
                            <span
                                class="font-medium text-gray-800 text-sm">{{ $invoice->student->name ?? ($child->name ?? '—') }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                            <span class="text-ris-gray text-sm">শ্রেণি</span>
                            <span
                                class="font-medium text-gray-800 text-sm">{{ $invoice->student->class->name ?? ($child->class->name ?? '—') }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                            <span class="text-ris-gray text-sm">ইনভয়েস নং</span>
                            <span
                                class="font-medium text-gray-800 text-sm">#{{ $invoice->invoice_no ?? $invoice->id }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                            <span class="text-ris-gray text-sm">ইনভয়েস তারিখ</span>
                            <span
                                class="font-medium text-gray-800 text-sm">{{ isset($invoice->date) ? \Carbon\Carbon::parse($invoice->date)->format('d M, Y') : '—' }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                            <span class="text-ris-gray text-sm">শেষ তারিখ</span>
                            <span
                                class="font-medium text-gray-800 text-sm">{{ isset($invoice->due_date) ? \Carbon\Carbon::parse($invoice->due_date)->format('d M, Y') : '—' }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                            <span class="text-ris-gray text-sm">ফি ধরন</span>
                            <span
                                class="font-medium text-gray-800 text-sm">{{ $invoice->fee_type ?? ($invoice->description ?? '—') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Fee Breakdown --}}
                <div class="card p-6">
                    <h3 class="font-heading font-bold text-ris-dark mb-5">ফি বিবরণ</h3>
                    @if (!empty($invoice->items) && count($invoice->items) > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th
                                            class="text-left py-3 font-heading font-semibold text-ris-dark text-xs uppercase">
                                            বিবরণ</th>
                                        <th
                                            class="text-right py-3 font-heading font-semibold text-ris-dark text-xs uppercase">
                                            পরিমাণ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invoice->items as $item)
                                        <tr class="border-b border-gray-50">
                                            <td class="py-3 text-gray-800">{{ $item->description ?? ($item->name ?? '—') }}
                                            </td>
                                            <td class="py-3 text-right font-medium text-gray-800">
                                                ৳{{ number_format($item->amount ?? 0, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-ris-gray text-center py-4">কোনো আইটেম নেই।</p>
                    @endif
                </div>
            </div>

            {{-- Payment Summary Sidebar --}}
            <div class="space-y-6">
                <div class="card p-6">
                    <h3 class="font-heading font-bold text-ris-dark mb-5">পরিশোধ সারসংক্ষেপ</h3>
                    <div class="space-y-4">
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-xs text-ris-gray mb-1">মোট পরিমাণ</p>
                            <p class="text-2xl font-heading font-bold text-ris-dark">
                                ৳{{ number_format($invoice->total_amount ?? 0, 2) }}</p>
                        </div>
                        <div class="p-4 bg-emerald-50 rounded-xl">
                            <p class="text-xs text-ris-gray mb-1">পরিশোধিত</p>
                            <p class="text-2xl font-heading font-bold text-emerald-600">
                                ৳{{ number_format($invoice->paid_amount ?? 0, 2) }}</p>
                        </div>
                        <div class="p-4 bg-red-50 rounded-xl">
                            <p class="text-xs text-ris-gray mb-1">বকেয়া</p>
                            <p class="text-2xl font-heading font-bold text-red-600">
                                ৳{{ number_format($invoice->due_amount ?? 0, 2) }}</p>
                        </div>
                    </div>
                </div>

                {{-- Payment History --}}
                @if (!empty($invoice->payments) && count($invoice->payments) > 0)
                    <div class="card p-6">
                        <h3 class="font-heading font-bold text-ris-dark mb-4">পরিশোধের ইতিহাস</h3>
                        <div class="space-y-3">
                            @foreach ($invoice->payments as $payment)
                                <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                                    <div
                                        class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center shrink-0 mt-0.5">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-800">
                                            ৳{{ number_format($payment->amount ?? 0, 2) }}</p>
                                        <p class="text-xs text-ris-gray">
                                            {{ isset($payment->date) ? \Carbon\Carbon::parse($payment->date)->format('d M, Y') : '—' }}
                                        </p>
                                        @if (isset($payment->method))
                                            <p class="text-xs text-ris-gray">{{ $payment->method }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Print Button --}}
                <button onclick="window.print()" class="w-full btn-secondary text-sm py-2.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    ইনভয়েস প্রিন্ট করুন
                </button>
            </div>
        </div>

    </div>

@endsection
