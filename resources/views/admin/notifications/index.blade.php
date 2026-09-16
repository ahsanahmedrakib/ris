@extends('layouts.admin')

@section('title', 'বিজ্ঞপ্তি')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-heading font-bold text-gray-900">বিজ্ঞপ্তি</h1>
            <p class="text-sm text-gray-500 mt-1">ওয়েবসাইটের ফর্ম থেকে প্রাপ্ত নতুন আবেদন ও বার্তা</p>
        </div>
        <form method="POST" action="{{ route('admin.notifications.read-all') }}">
            @csrf
            <button type="submit"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-ris-primary text-white text-sm font-medium rounded-lg hover:bg-ris-dark transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                সব পড়া হয়েছে
            </button>
        </form>
    </div>

    {{-- Data Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="gradient-logo">
                        <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">ক্রমিক</th>
                        <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">বিজ্ঞপ্তি</th>
                        <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">তারিখ</th>
                        <th class="text-left px-4 py-3.5 font-medium text-white whitespace-nowrap">অবস্থা</th>
                        <th class="text-center px-4 py-3.5 font-medium text-white whitespace-nowrap sticky right-0 bg-linear-to-r from-ris-light to-ris-dark z-10">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($notifications as $index => $notification)
                        @php
                            $data = $notification->data;
                            $typeLabels = [
                                'admission' => 'ভর্তি আবেদন',
                                'scholarship' => 'মেধাবৃত্তি',
                                'testimonial' => 'শুভকামনা ও মতামত',
                                'contact' => 'কনটাক্ট মেসেজ',
                            ];
                            $typeColors = [
                                'admission' => 'bg-blue-50 text-blue-700',
                                'scholarship' => 'bg-purple-50 text-purple-700',
                                'testimonial' => 'bg-green-50 text-green-700',
                                'contact' => 'bg-amber-50 text-amber-700',
                            ];
                            $type = $data['type'] ?? 'other';
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors {{ $notification->read_at ? '' : 'bg-amber-50/40' }}">
                            <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ ($notifications->currentPage() - 1) * $notifications->perPage() + $index + 1 }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-start gap-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium shrink-0 mt-0.5 {{ $typeColors[$type] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ $typeLabels[$type] ?? 'বিজ্ঞপ্তি' }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-900">{{ $data['title'] ?? '' }}</p>
                                        <p class="text-gray-500 text-xs mt-0.5 truncate">{{ $data['message'] ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $notification->created_at->format('d/m/Y h:i A') }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($notification->read_at)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">পঠিত</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">অপঠিত</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 sticky right-0 bg-white z-10">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('admin.notifications.read', $notification) }}"
                                        class="p-1.5 rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors" title="খুলুন">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                <p class="text-gray-500 font-medium">কোনো বিজ্ঞপ্তি নেই</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('vendor.pagination.custom', ['paginator' => $notifications])
    </div>

</div>
@endsection