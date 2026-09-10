@extends('layouts.admin')

@section('title', 'ফি ব্যবস্থাপনা')

@section('content')
    <div class="space-y-6">

        <div>
            <h1 class="text-2xl font-heading font-bold text-gray-900">ফি ব্যবস্থাপনা</h1>
            <p class="text-sm text-gray-500 mt-1">শিক্ষার্থীদের ফি সংগ্রহ ও বকেয় পরিচালনা করুন</p>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 lg:gap-6">
            <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">মোট সংগৃহীত</p>
                        <p class="text-2xl font-heading font-bold text-emerald-600 mt-1">
                            ৳{{ number_format($totalCollected ?? 3200000) }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">বকেয়</p>
                        <p class="text-2xl font-heading font-bold text-amber-600 mt-1">
                            ৳{{ number_format($totalPending ?? 890000) }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">মেয়াদোত্তীর্ণ</p>
                        <p class="text-2xl font-heading font-bold text-red-600 mt-1">
                            ৳{{ number_format($totalOverdue ?? 340000) }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Links --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <a href="{{ route('admin.fees.invoices') }}"
                class="flex items-center gap-4 p-5 bg-white rounded-xl border border-gray-200 hover:border-ris-primary hover:shadow-md transition-all group">
                <div
                    class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center group-hover:bg-ris-primary/10 transition-colors">
                    <svg class="w-6 h-6 text-blue-600 group-hover:text-ris-primary transition-colors" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-medium text-gray-900">ইনভয়েস</h3>
                    <p class="text-xs text-gray-500">ইনভয়েস তালিকা ও ব্যবস্থাপনা</p>
                </div>
            </a>
            <a href="{{ route('admin.fees.payments') }}"
                class="flex items-center gap-4 p-5 bg-white rounded-xl border border-gray-200 hover:border-ris-primary hover:shadow-md transition-all group">
                <div
                    class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center group-hover:bg-ris-primary/10 transition-colors">
                    <svg class="w-6 h-6 text-emerald-600 group-hover:text-ris-primary transition-colors" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-medium text-gray-900">পেমেন্ট ইতিহাস</h3>
                    <p class="text-xs text-gray-500">সকল পেমেন্টের ইতিহাস দেখুন</p>
                </div>
            </a>
            <div
                class="flex items-center gap-4 p-5 bg-white rounded-xl border border-gray-200 hover:border-ris-primary hover:shadow-md transition-all group cursor-pointer">
                <div
                    class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center group-hover:bg-ris-primary/10 transition-colors">
                    <svg class="w-6 h-6 text-amber-600 group-hover:text-ris-primary transition-colors" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-medium text-gray-900">ফি স্ট্রাকচার</h3>
                    <p class="text-xs text-gray-500">ফি স্ট্রাকচার পরিচালনা করুন</p>
                </div>
            </div>
        </div>

        {{-- Fee Structures --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-heading font-semibold text-gray-900">ফি স্ট্রাকচার</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">ক্রমিক</th>
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">ফি ধরন</th>
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">শ্রেণি</th>
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">পরিমাণ</th>
                            <th class="text-left px-5 py-3.5 font-medium text-gray-500">শিক্ষাবর্ষ</th>
                            <th class="text-right px-5 py-3.5 font-medium text-gray-500">কার্যক্রম</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse(($feeStructures ?? []) as $index => $structure)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3.5 text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-5 py-3.5 font-medium text-gray-900">{{ $structure->name }}</td>
                                <td class="px-5 py-3.5 text-gray-600">{{ $structure->class->name ?? '-' }}</td>
                                <td class="px-5 py-3.5 text-gray-900 font-medium">৳{{ number_format($structure->amount) }}
                                </td>
                                <td class="px-5 py-3.5 text-gray-600">{{ $structure->academicYear->name ?? '-' }}</td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="#"
                                            class="p-1.5 rounded-lg text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition-colors"
                                            title="সম্পাদনা">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form method="POST" action="#"
                                            onsubmit="return confirm('আপনি কি নিশ্চিত?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors"
                                                title="মুছুন">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-gray-400 text-sm">কোনো ফি স্ট্রাকচার
                                    নেই</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
