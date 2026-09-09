@extends('layouts.parent')

@section('title', 'ফি — অভিভাবক পোর্টাল')
@section('page-title', 'ফি')

@section('content')

<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="font-heading font-bold text-xl text-ris-dark">ফি</h2>
            <p class="text-sm text-ris-gray mt-1">সন্তানের ফি অবস্থা ও পরিশোধের ইতিহাস</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card p-4 sm:p-5">
        <form method="GET" action="{{ route('parent.fees') }}" class="flex flex-col sm:flex-row items-stretch sm:items-end gap-4">
            @if(!empty($children) && count($children) > 1)
                <div class="flex-1">
                    <label class="block text-xs font-medium text-ris-gray mb-1.5">সন্তান নির্বাচন করুন</label>
                    <select name="child_id" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-ris-primary/30 focus:border-ris-primary transition-all">
                        @foreach($children as $child)
                            <option value="{{ $child->id }}" {{ (request('child_id', $selected_child_id ?? null) == $child->id) ? 'selected' : '' }}>
                                {{ $child->name }} — {{ $child->class->name ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
            <button type="submit" class="btn-primary py-2.5 px-5 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                খুঁজুন
            </button>
        </form>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="card p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-ris-gray">মোট বকেয়া</p>
                    <p class="text-xl font-heading font-bold text-red-600">৳{{ number_format($summary['total_due'] ?? 0, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="card p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-ris-gray">পরিশোধিত</p>
                    <p class="text-xl font-heading font-bold text-emerald-600">৳{{ number_format($summary['total_paid'] ?? 0, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="card p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-ris-gray">অপেক্ষমাণ</p>
                    <p class="text-xl font-heading font-bold text-amber-600">৳{{ number_format($summary['total_pending'] ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Invoice Table --}}
    <div class="card overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-heading font-bold text-ris-dark">ইনভয়েস তালিকা</h3>
            <a href="{{ route('parent.fees.history') }}" class="text-sm font-medium text-ris-primary hover:text-ris-dark transition-colors">
                পরিশোধের ইতিহাস →
            </a>
        </div>

        @if(!empty($invoices) && count($invoices) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="text-left py-3 px-5 font-heading font-semibold text-ris-dark text-xs uppercase tracking-wider">ইনভয়েস নং</th>
                            <th class="text-left py-3 px-5 font-heading font-semibold text-ris-dark text-xs uppercase tracking-wider">বিবরণ</th>
                            <th class="text-center py-3 px-5 font-heading font-semibold text-ris-dark text-xs uppercase tracking-wider">মোট পরিমাণ</th>
                            <th class="text-center py-3 px-5 font-heading font-semibold text-ris-dark text-xs uppercase tracking-wider">বকেয়া</th>
                            <th class="text-left py-3 px-5 font-heading font-semibold text-ris-dark text-xs uppercase tracking-wider">তারিখ</th>
                            <th class="text-center py-3 px-5 font-heading font-semibold text-ris-dark text-xs uppercase tracking-wider">অবস্থা</th>
                            <th class="text-center py-3 px-5 font-heading font-semibold text-ris-dark text-xs uppercase tracking-wider">কার্যক্রম</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices as $invoice)
                            <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                                <td class="py-3.5 px-5 font-medium text-gray-800">#{{ $invoice->invoice_no ?? $invoice->id }}</td>
                                <td class="py-3.5 px-5 text-gray-600 max-w-[200px] truncate">{{ $invoice->description ?? $invoice->fee_type ?? '—' }}</td>
                                <td class="py-3.5 px-5 text-center font-medium text-gray-800">৳{{ number_format($invoice->total_amount ?? 0, 2) }}</td>
                                <td class="py-3.5 px-5 text-center">
                                    @if(($invoice->due_amount ?? 0) > 0)
                                        <span class="font-semibold text-red-600">৳{{ number_format($invoice->due_amount, 2) }}</span>
                                    @else
                                        <span class="text-emerald-600 font-medium">০.০০</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-5 text-gray-600">{{ isset($invoice->date) ? \Carbon\Carbon::parse($invoice->date)->format('d M, Y') : '—' }}</td>
                                <td class="py-3.5 px-5 text-center">
                                    @if(isset($invoice->status) && $invoice->status === 'paid')
                                        <span class="badge-success badge text-xs">পরিশোধিত</span>
                                    @elseif(isset($invoice->status) && $invoice->status === 'partial')
                                        <span class="badge-warning badge text-xs">আংশিক</span>
                                    @else
                                        <span class="badge-danger badge text-xs">বকেয়া</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-5 text-center">
                                    <a href="{{ route('parent.fees.show', $invoice->id) }}" class="text-ris-primary hover:text-ris-dark transition-colors text-xs font-medium">
                                        বিস্তারিত
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-gray-100">
                {{ $invoices->withQueryString()->links() }}
            </div>
        @else
            <div class="p-10 text-center">
                <div class="w-16 h-16 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h4 class="font-heading font-bold text-ris-dark">কোনো ইনভয়েস নেই</h4>
                <p class="text-sm text-ris-gray mt-1">নির্বাচিত সন্তানের জন্য কোনো ফি ইনভয়েস পাওয়া যায়নি।</p>
            </div>
        @endif
    </div>

</div>

@endsection
