@extends('layouts.admin')

@section('title', 'মেধাবৃত্তি রেজিস্ট্রেশন তালিকা')

@section('content')
<div class="space-y-6">

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-heading font-bold text-gray-900">মেধাবৃত্তি রেজিস্ট্রেশন</h1>
            <p class="text-sm text-gray-500 mt-1">সকল মেধাবৃত্তি রেজিস্ট্রেশন পরিচালনা করুন</p>
        </div>
        <a href="{{ route('admin.scholarship.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            নতুন রেজিস্ট্রেশন
        </a>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" action="{{ route('admin.scholarship.index') }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">অনুসন্ধান</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="নাম বা রেজি নং দিয়ে খুঁজুন..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">শ্রেণি</label>
                    <select name="class_no" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                        <option value="">সকল শ্রেণি</option>
                        @foreach($classes as $value => $label)
                            <option value="{{ $value }}" {{ request('class_no') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors">
                        ফিল্টার করুন
                    </button>
                    <a href="{{ route('admin.scholarship.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                        রিসেট
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Data Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">ক্রমিক</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">রেজি নং</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">শিক্ষার্থীর নাম</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">শ্রেণি</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">রোল নং</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">মোবাইল</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">পেমেন্ট</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">তারিখ</th>
                        <th class="text-right px-5 py-3.5 font-medium text-gray-500">কার্যক্রম</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($registrations as $index => $registration)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3.5 text-gray-500">{{ ($registrations->currentPage() - 1) * $registrations->perPage() + $index + 1 }}</td>
                            <td class="px-5 py-3.5">
                                <a href="{{ route('admin.scholarship.show', $registration) }}" class="font-heading font-semibold text-ris-primary hover:underline">{{ $registration->registration_no }}</a>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-ris-primary/10 flex items-center justify-center text-ris-primary text-xs font-semibold shrink-0">
                                        {{ mb_substr($registration->student_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $registration->student_name }}</div>
                                        <div class="text-xs text-gray-400">{{ $registration->school_name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $classes[$registration->class_no] ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $registration->roll_no ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $registration->mobile_no }}</td>
                            <td class="px-5 py-3.5 text-gray-600">
                                @if ($registration->payment_method === 'cash')
                                    <span class="text-emerald-600 font-medium">নগদ</span>
                                @else
                                    {{ $registration->bkash_no }}
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-gray-500">{{ $registration->created_at->format('d/m/Y') }}</td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.scholarship.show', $registration) }}" class="p-1.5 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors" title="দেখুন">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('admin.scholarship.pdf', $registration) }}" target="_blank" class="p-1.5 rounded-lg text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 transition-colors" title="PDF দেখুন ও প্রিন্ট করুন">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.scholarship.destroy', $registration) }}" onsubmit="return confirm('আপনি কি নিশ্চিত এই রেজিস্ট্রেশনটি মুছে ফেলতে চান?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="মুছুন">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-12 text-center">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                <p class="text-gray-500 font-medium">কোনো রেজিস্ট্রেশন পাওয়া যায়নি</p>
                                <p class="text-sm text-gray-400 mt-1">নতুন রেজিস্ট্রেশন করুন বা ফিল্টার পরিবর্তন করুন</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($registrations->hasPages())
            <div class="px-5 py-3 border-t border-gray-100">
                {{ $registrations->links() }}
            </div>
        @endif
    </div>

</div>
@endsection