@extends('layouts.admin')

@section('title', 'ইনভয়েস তালিকা')

@section('content')
<div class="space-y-6">

    <div class="flex items-center gap-4">
        <a href="{{ route('admin.fees.index') }}" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-heading font-bold text-gray-900">ইনভয়েস তালিকা</h1>
            <p class="text-sm text-gray-500 mt-1">সকল ইনভয়েস দেখুন ও পরিচালনা করুন</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">ইনভয়েস নং</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">ছাত্রের নাম</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">শ্রেণী</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">মোট পরিমাণ</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">পরিশোধিত</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">বকেয়</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">শেষ তারিখ</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">অবস্থা</th>
                        <th class="text-right px-5 py-3.5 font-medium text-gray-500">কার্যক্রম</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse(($invoices ?? []) as $invoice)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3.5 font-medium text-gray-900">{{ $invoice->invoice_no }}</td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-ris-primary/10 flex items-center justify-center text-ris-primary text-xs font-semibold shrink-0">
                                        {{ substr($invoice->student->name_bn ?? 'ছ', 0, 1) }}
                                    </div>
                                    <span class="text-gray-900">{{ $invoice->student->name_bn ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $invoice->student->class->name ?? '-' }}</td>
                            <td class="px-5 py-3.5 font-medium text-gray-900">৳{{ number_format($invoice->total_amount) }}</td>
                            <td class="px-5 py-3.5 text-emerald-600 font-medium">৳{{ number_format($invoice->paid_amount) }}</td>
                            <td class="px-5 py-3.5 text-red-600 font-medium">৳{{ number_format($invoice->total_amount - $invoice->paid_amount) }}</td>
                            <td class="px-5 py-3.5 text-gray-600">{{ \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y') }}</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $invoice->status === 'paid' ? 'bg-emerald-50 text-emerald-700' : ($invoice->status === 'partial' ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700') }}">
                                    {{ $invoice->status === 'paid' ? 'পরিশোধিত' : ($invoice->status === 'partial' ? 'আংশিক' : 'বকেয়') }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="#" class="p-1.5 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors" title="দেখুন">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="#" class="p-1.5 rounded-lg text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 transition-colors" title="পেমেন্ট">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-12 text-center">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                                <p class="text-gray-500 font-medium">কোনো ইনভয়েস নেই</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($invoices) && $invoices instanceof \Illuminate\Pagination\LengthAwarePaginator && $invoices->hasPages())
            <div class="px-5 py-3 border-t border-gray-100">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>

</div>
@endsection