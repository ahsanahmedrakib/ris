@extends('layouts.admin')

@section('title', 'অ্যাক্টিভিটি লগ')

@section('content')
    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-heading font-bold text-gray-900">অ্যাক্টিভিটি লগ</h1>
                <p class="text-sm text-gray-500 mt-1">সিস্টেমের সকল লগইনকারীর কাজের হিসাব</p>
            </div>
            <div class="flex items-center gap-2">
                <span
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-sm font-medium rounded-lg text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    সর্বমোট লগ: {{ $logs->total() }}
                </span>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <form method="GET" action="{{ route('admin.activity-logs.index') }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">অনুসন্ধান</label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="ইউজার বা বিবরণ দিয়ে খুঁজুন..."
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">অ্যাকশন</label>
                        <select name="action"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                            <option value="">সকল অ্যাকশন</option>
                            @foreach ($actions as $value => $label)
                                <option value="{{ $value }}" {{ request('action') == $value ? 'selected' : '' }}>
                                    {{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">মডিউল</label>
                        <select name="model"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-ris-primary/20 focus:border-ris-primary outline-none transition-colors bg-white">
                            <option value="">সকল মডিউল</option>
                            @foreach ($models as $value => $label)
                                <option value="{{ $value }}" {{ request('model') == $value ? 'selected' : '' }}>
                                    {{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit"
                            class="px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors cursor-pointer">
                            ফিল্টার করুন
                        </button>
                        <a href="{{ route('admin.activity-logs.index') }}"
                            class="px-4 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
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
                        <tr class="gradient-logo">
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">ক্রমিক</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">ইউজার</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">অ্যাকশন</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">বিবরণ</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">আইপি ঠিকানা</th>
                            <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">সময়</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($logs as $index => $log)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                                    {{ ($logs->currentPage() - 1) * $logs->perPage() + $index + 1 }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-7 h-7 rounded-full bg-ris-primary/10 flex items-center justify-center text-ris-primary text-xs font-semibold shrink-0">
                                            {{ mb_substr($log->user_name ?? 'অ', 0, 1) }}
                                        </div>
                                        <span class="font-medium text-gray-900">{{ $log->user_name ?? 'অজানা' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if ($log->action === 'create')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">তৈরি</span>
                                    @elseif($log->action === 'update')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">আপডেট</span>
                                    @elseif($log->action === 'delete')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">মুছে
                                            ফেলা</span>
                                    @elseif($log->action === 'force_delete')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-700 text-white">স্থায়ী
                                            মুছে ফেলা</span>
                                    @elseif($log->action === 'restore')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">পুনরুদ্ধার</span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ $log->action }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $log->description }}</td>
                                <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $log->ip_address ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                                    {{ $log->created_at->format('d/m/Y h:i A') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center">
                                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-gray-500 font-medium">কোনো লগ পাওয়া যায়নি</p>
                                    <p class="text-sm text-gray-400 mt-1">সিস্টেমে কাজ করার পর লগ তৈরি হবে</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @include('vendor.pagination.custom', ['paginator' => $logs])
        </div>
    </div>
@endsection
