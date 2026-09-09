@extends('layouts.admin')

@section('title', 'ফি চালান')

@section('content')
<div class="space-y-6">

    <div class="flex items-center gap-4">
        <a href="{{ route('admin.fees.invoices') }}" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-heading font-bold text-gray-900">{{ $invoice->invoice_no ?? 'ফি চালান' }}</h1>
            <p class="text-sm text-gray-500 mt-1">চালানের বিস্তারিত তথ্য ও পেমেন্ট</p>
        </div>
    </div>

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm text-red-800 flex items-center gap-3">
            <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <h2 class="font-heading font-semibold text-gray-900">চালান তথ্য</h2>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                    {{ $invoice->status === 'paid' ? 'bg-emerald-50 text-emerald-700' : ($invoice->status === 'partial' ? 'bg-amber-50 text-amber-700' : ($invoice->status === 'overdue' ? 'bg-red-50 text-red-700' : 'bg-gray-100 text-gray-700')) }}">
                    {{ $invoice->status === 'paid' ? 'পরিশোধিত' : ($invoice->status === 'partial' ? 'আংশিক' : ($invoice->status === 'overdue' ? 'মেয়াদোত্তীর্ণ' : 'মুলতুবি')) }}
                </span>
            </div>
            <form method="POST" action="{{ route('admin.fees.destroy', $invoice) }}" onsubmit="return confirm('আপনি কি নিশ্চিত এই চালানটি মুছে ফেলতে চান?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-2 px-3 py-1.5 text-sm text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    মুছুন
                </button>
            </form>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <p class="text-xs font-medium text-gray-400">ছাত্র/ছাত্রী</p>
                    <p class="text-sm text-gray-700 mt-1 font-medium">{{ $invoice->student->user->name ?? '-' }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">রোল: {{ $invoice->student->roll_no ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400">ফি ধরণ</p>
                    <p class="text-sm text-gray-700 mt-1">{{ $invoice->feeStructure->fee_type ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400">মোট পরিমাণ</p>
                    <p class="text-sm text-gray-700 mt-1 font-medium">৳{{ number_format($invoice->amount, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400">পরিশোধিত</p>
                    <p class="text-sm text-gray-700 mt-1">৳{{ number_format($invoice->paid_amount, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400">বকেয়া</p>
                    <p class="text-sm text-red-600 mt-1 font-medium">৳{{ number_format($invoice->due_amount, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400">শেষ তারিখ</p>
                    <p class="text-sm text-gray-700 mt-1">{{ $invoice->due_date?->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-heading font-semibold text-gray-900">পেমেন্ট রেকর্ড করুন</h2>
        </div>
        <div class="p-5">
            <form method="POST" action="{{ route('admin.fees.payments.record') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">পরিমাণ (৳) <span class="text-red-500">*</span></label>
                        <input type="number" name="amount" value="{{ old('amount') }}" required min="0.01" max="{{ $invoice->due_amount }}" step="0.01"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">পেমেন্ট পদ্ধতি <span class="text-red-500">*</span></label>
                        <select name="payment_method" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                            <option value="cash" {{ old('payment_method', 'cash') === 'cash' ? 'selected' : '' }}>নগদ</option>
                            <option value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>ব্যাংক ট্রান্সফার</option>
                            <option value="mobile_banking" {{ old('payment_method') === 'mobile_banking' ? 'selected' : '' }}>মোবাইল ব্যাংকিং</option>
                            <option value="cheque" {{ old('payment_method') === 'cheque' ? 'selected' : '' }}>চেক</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">ট্রানজেকশন আইডি</label>
                        <input type="text" name="transaction_id" value="{{ old('transaction_id') }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">
                            পেমেন্ট রেকর্ড করুন
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection