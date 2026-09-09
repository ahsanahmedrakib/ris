@extends('layouts.admin')

@section('title', 'কর্মচারী রিপোর্ট')

@section('content')
<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-heading font-bold text-gray-900">কর্মচারী রিপোর্ট</h1>
        <p class="text-sm text-gray-500 mt-1">কর্মচারীদের তালিকা, পদবি, বিভাগ ও বেতনের রিপোর্ট দেখুন</p>
    </div>

    {{-- Staff Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-heading font-semibold text-gray-900">কর্মচারী তালিকা</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">ক্রমিক</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">আইডি</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">নাম</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">পদবি</th>
                        <th class="text-left px-5 py-3.5 font-medium text-gray-500">বিভাগ</th>
                        <th class="text-right px-5 py-3.5 font-medium text-gray-500">বেতন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($staff as $index => $member)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3.5 text-gray-500">{{ ($staff->currentPage() - 1) * $staff->perPage() + $index + 1 }}</td>
                            <td class="px-5 py-3.5 font-mono text-xs text-gray-500">{{ $member->staff_id }}</td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-ris-primary/10 flex items-center justify-center text-ris-primary text-xs font-semibold shrink-0">
                                        {{ substr($member->name, 0, 1) }}
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $member->name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $member->designation ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $member->department ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-right text-gray-900 font-medium">৳{{ number_format($member->salary ?? 0) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <p class="text-gray-500 font-medium">কোনো কর্মচারী পাওয়া যায়নি</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($staff->hasPages())
            <div class="px-5 py-3 border-t border-gray-100">
                {{ $staff->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
